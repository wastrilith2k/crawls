package jCrawler;

public class logger {
	// Move this into a logging class
	public static void print (String msg, Object... args) {
		System.out.println(String.format(msg, args));
	}
}
