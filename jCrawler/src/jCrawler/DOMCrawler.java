package jCrawler;

import org.jsoup.Jsoup;
import org.jsoup.helper.Validate;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;
import jCrawler.logger;
import jCrawler.util;
import jCrawler.DB;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URISyntaxException;
import java.net.URL;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class DOMCrawler {

	// Initialize new object with initial crawl URL
	public DOMCrawler(String initialUrl) throws IOException {
		crawlURL(getURLID(initialUrl));
	}
	
	// Crawl the URL
	private void crawlURL(int urlid) throws IOException {
		String url = "";		
		
		// Have we hit the throttling limit?
		
		// Get the URL for this ID
		url = getURL(urlid);
		logger.print("URL for id %s is %s", urlid, url);
		
		// Get the DOM		
		Document doc = Jsoup.connect(url).get();
		
		// URL Title		
	    Elements titles = doc.select("title");	    
	    for (Element title : titles) {
	    	try {
				DB.query("UPDATE url SET title = '" + title.text() + "' WHERE url_id = " + urlid);
			} catch (SQLException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
	    	logger.print(" * title: <%s>  (%s)", url, title.text());
	    }
		
		// Obtain links		
	    Elements links = doc.select("a[href]");	    
	    for (Element link : links) {
	    	// Call addURL which will determine whitelisting,blacklisting, content type, and response code
	    	addURL(link.attr("abs:href"));	    	
	    	addURLRelationship(urlid, getURLID(link.attr("abs:href")), link.text());
	    	logger.print(" * a: <%s>  (%s)", link.attr("abs:href"), util.trim(link.text(), 35));
	    }
	    
	}
	
	/*
	 * Set the URL as being crawled
	 */
	
	private void setAsCrawled(int urlID) {
		setAsCrawled(urlID, 200);
	}
	
	private void setAsCrawled(int urlID, int returnCode) {		
		try {
			DB.query("UPDATE url SET crawled = 1, return_code = " + returnCode + " WHERE url_id = " + urlID);
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}		
	}	
	
	/*
	 * Get the URLID for this URL 
	 */
	private int getURLID(String url) {
		url = util.stripHash(url);
		int urlid = -1;
		ResultSet rs;
		try {
			rs = DB.result("SELECT url_id FROM url WHERE url = '" + url + "'");
			// URL in database
			if (rs != null && rs.next()) {
				try {
					urlid = Integer.parseInt(rs.getString(1));
				}
				catch (Exception e) {					
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			// URL not in database
            } else {
            	// Add URL to database
            	urlid = DB.update("INSERT INTO url (url) VALUES ('" + url + "')");
            }
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return urlid;
	}

	/*
	 * Get the URL for this URLID 
	 */
	private String getURL(int urlid) {
		String url = "";
		ResultSet rs;
		try {
			rs = DB.result("SELECT url FROM url WHERE url_id = " + urlid);
			// URL in database
			if (rs != null && rs.next()) {
				try {
					url = rs.getString(1);
				}
				catch (Exception e) {					
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			// URL not in database
            }
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return url;
	}

	/*
	 * 
	 */
	
	private void addURLRelationship(int sourceID, int destID, String linkText) {
		try {
			DB.query("INSERT INTO url_relationship (source_id, destination_id, linktext) VALUES (" + sourceID + ", " + destID + ", '" + linkText + "')");
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}				
	}
	
	
	/*
	 * Method for getting the file contents if no content type is passed
	 */
	private void addURL(String url) {	
		List<String> contentTypes = new ArrayList<String>();
		contentTypes.add("text/html");
		addURL(url, contentTypes);
	}
	
	/*
	 * Method for getting file contents if content type(s) are passed
	 */
	private boolean addURL(String urlstr, List<String> contentTypes) {		
		URL url;
		
		try {
			if (util.is_blacklisted(urlstr)) {
				logger.print("Set as crawled: %s is blacklisted", urlstr);
				setAsCrawled(getURLID(urlstr));
			}
			if (!util.is_whitelisted(urlstr)) {
				logger.print("Set as crawled: %s is not whitelisted", urlstr);
				setAsCrawled(getURLID(urlstr));
			}
		} catch (URISyntaxException e1) {
			// TODO Auto-generated catch block			
			e1.printStackTrace();
			return false;
		}
		
		try {
			url = new URL(urlstr);
			HttpURLConnection connection = (HttpURLConnection)  url.openConnection();
			connection.setRequestMethod("HEAD");
			connection.setInstanceFollowRedirects(true);
			connection.connect();
			
			// Determine that this is a valid response code and MIME type
			String contentType = connection.getContentType();
			int responseCode = connection.getResponseCode();			
			connection.disconnect();
			
			// Check for bad response codes
			if (responseCode > 400) {
				logger.print("Set as crawled: %s had a response code of %i", urlstr, responseCode);
				setAsCrawled(getURLID(urlstr), responseCode);
				return false;
			}
			
			// check for valid content types
			if (util.validContentType(contentType, contentTypes)) {
				logger.print("Set as crawled: %s had a content type of %s", urlstr, contentType);
				setAsCrawled(getURLID(urlstr), responseCode);
				return false;
			}
			
			// Handle redirection
			if (responseCode >= 300) {
				addURL(connection.getHeaderField("Location"), contentTypes);
			} else {
				// Add the URL
				getURLID(urlstr);
			}
			return true;
		}
		catch (IOException e) {			
			e.printStackTrace();
			return false;		
		}		
	}
}
