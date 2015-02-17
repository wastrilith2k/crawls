package jCrawler;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.logging.Logger;

public class DB {
    static Connection con = null;
    
    static String url = "jdbc:mysql://localhost:3306/crawls";
    static String user = "root";
    static String password = "Ensabahnur1!";
    
    private static void connect() {    	
    	try {
			con = DriverManager.getConnection(url, user, password);
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    }
    
    public static void query(String qry) throws SQLException {
    	Statement st = null;
    	ResultSet rs = null;
    	try {
    		if (con == null) { connect(); }
    		st = con.createStatement();
            rs = st.executeQuery(qry);
    	}
    	catch (SQLException e) {
    		// Do nothing
    	}
    }
    
    public static ResultSet result(String qry) throws SQLException {
    	qry += ";";
    	Statement st = null;
    	ResultSet rs = null;
    	try {
    		if (con == null) { connect(); }
    		st = con.createStatement();
    		rs = st.executeQuery(qry);
    	}
    	catch (SQLException e) {
    		logger.print("Error in DB.result() with query %s", qry);
    	}
    	catch (NullPointerException npe) {
    		logger.print("Null Pointer exception in DB.result() with query %s", qry);
    	}
    	return rs;
    }
    
    public static int update(String qry) {
    	Statement st = null;
    	ResultSet rs = null;
    	int id = -1;
    	try {
    		if (con == null) { connect(); }
    		st = con.createStatement();
            int numRes = st.executeUpdate(qry, Statement.RETURN_GENERATED_KEYS);
            rs = st.getGeneratedKeys();
            if (rs.next()){
                id = rs.getInt(1);
            }
            rs.close();
            st.close();
    	}
    	catch (SQLException e) {
    		logger.print("SQLException in DB.update() with query %s", qry);
    	}
    	return id;
    }
    
}
