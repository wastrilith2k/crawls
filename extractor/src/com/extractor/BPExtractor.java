package com.extractor;
import java.net.URL;
import de.l3s.boilerpipe.extractors.ArticleExtractor;

public class BPExtractor {
	public static void main(String[] args)  {
		try {
			//if (args.length > 0 ) {
			  final URL url = new URL(args[0]);
			  // NOTE: Use ArticleExtractor unless DefaultExtractor gives better results for you
			  String text = ArticleExtractor.INSTANCE.getText(url);
			  System.out.println(text);
			//}
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}

}
