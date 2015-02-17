package jCrawler;

import java.util.ArrayList;
import java.util.List;

public class config {
	
	private static int depth;
	private static int throttle;
	private static int successful_crawls;
	private static int max_concurrent_crawls;
	private static List<String> blacklist = new ArrayList<String>();
	private static List<String> whitelist = new ArrayList<String>();
	
	// If no arguments are passed, use the default values
	public static void setConfig() {
		setConfig(2,10,4);
	}	
	
	public static void setConfig(int d, int t, int mcc){
		depth = d;
		throttle = t;
		max_concurrent_crawls = mcc;
		successful_crawls = 0;
	}
	
	protected static void successful_crawl() {
		successful_crawls++;
	}
	
	protected static int get_depth() {
		return depth;
	}

	protected static int get_throttle() {
		return throttle;
	}

	public static int get_max_concurrent_crawls() {
		return max_concurrent_crawls;
	}
	
	protected static boolean whitelist_exist() {
		if (whitelist.size() > 0) return true;
		return false;
	}

	protected static boolean blacklist_exist() {
		if (blacklist.size() > 0) return true;
		return false;
	}

	protected static void whitelist_set(String domain) {
		whitelist.add(domain);
	}

	protected static void blacklist_set(String domain) {
		blacklist.add(domain);
	}
	
	protected static List<String> whitelist_get() {
		return whitelist;
	}

	protected static List<String> blacklist_get() {
		return blacklist;
	}

}
