package jCrawler;
import java.io.IOException;

import jCrawler.config;
import jCrawler.DB;
import jCrawler.logger;
import jCrawler.DOMCrawler;
import jCrawler.util;

public class jCrawler {

	public static void main (String args[]) throws IOException {
		// Create first DOMCrawler
		config.setConfig();
		DOMCrawler crawler = new DOMCrawler("http://kb.webtrends.com/");
	}
}
