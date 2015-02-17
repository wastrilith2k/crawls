package jCrawler;

import java.net.URI;
import java.net.URISyntaxException;
import java.util.List;

import jCrawler.config;

public class util {
	
    public static String trim(String s, int width) {
        if (s.length() > width)
            return s.substring(0, width-1) + ".";
        else
            return s;
    }
    
    public static boolean is_whitelisted(String url) throws URISyntaxException {
    	// Check the length to see if we need to do this
    	if (!config.whitelist_exist()) return true;
    	
    	URI uri = new URI(url);
    	
    	return config.whitelist_get().contains(uri.getHost());
    }
    
    public static boolean is_blacklisted(String url) throws URISyntaxException {
    	// Check the length to see if we need to do this
    	if (config.blacklist_exist()) return true;
    	
    	URI uri = new URI(url);
    	
    	return config.blacklist_get().contains(uri.getHost());
    }
    
    public static String stripHash(String url) {
    	if (url.indexOf("#") > -1) {
    		return url.split("#")[0];
    	}
    	return url;
    }
    
    public static boolean validContentType(String contentType, List<String> contentTypes) {
    	for (int i = 0; i < contentTypes.size(); i++) {
    	  if (contentType.indexOf(contentTypes.get(i)) > -1) return true;	
    	}    	
    	return false;
    }

}
