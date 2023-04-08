/*
 * Created by Joshua Greenert  9/19/2020  Bellevue University  Assignment 5.1
 * 
 * This servlet will provide a form for the user to enter data into that will be 
 * entered into a database through an SQL connection; then the database contents will be
 * displayed after the page is displayed to the user in a formatted table.
 * 
 * TABLES: week5cars, week5bikes, week5colors
 */
import javax.servlet.*;
import javax.servlet.http.*;
import java.io.*;
import java.sql.*;

public class JoshGreenertFormPost2 extends HttpServlet {

    // Specify the target host that is the servlet.
    private String target = "localhost:7070/joshgreenertWeek5";

    // Add variables to set up the Java file.
    String car = "";
    String bike = "";
    String color = "";

    public void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {

        PrintWriter out = response.getWriter();

        printHeader(out);
        printForms1(out);
        printFooter(out);
    }

    public void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {

        PrintWriter out = response.getWriter();

        printHeader(out);
        printForms1(out);

        // Get the data from the form button by using ternary statements to account for null values.
        car = (request.getParameter("week5cars") != null) ? request.getParameter("week5cars") : "";
        bike = (request.getParameter("week5bikes") != null) ? request.getParameter("week5bikes") : "";
        color = (request.getParameter("week5colors") != null) ? request.getParameter("week5colors") : "";

        // Start the table where the data will be filled into.
        out.println("<div class='tablecss'>");
        out.println("<h2 style='text-align:center;'>Data From Query</h2>");
        out.println("<table style='border: 1px solid black; margin-left: auto; margin-right: auto; background:white;'>");
        out.println("<tr>");
        out.println("<th style='text-align:center; border: 1px solid #000000;'>ID</th>");
        out.println("<th style='text-align:center; border: 1px solid #000000;'>ITEM</th>");
        out.println("</tr>");
        out.println("<tr>");

        // Start up the MySQL database and get the data.
        try{

			// Create an index to create a start point.
			int index = 1;
            
            // Create a connection to use for statements.
			DriverManager.registerDriver (new oracle.jdbc.OracleDriver());
			Connection con = DriverManager.getConnection("jdbc:oracle:thin:@localhost", "joshgreenert1","iamsilly1");

            // Check which variable the user entered for which form was posted.
            if(color.isEmpty() && bike.isEmpty()){
                
                // Get the data from the table and to find the final row number; for primary key.
                Statement stmt = con.createStatement();
                ResultSet carRs = stmt.executeQuery("SELECT * FROM WEEK5CARS");
                int count = 1;

                // Set count by counting all records fetched.
                while(carRs.next()){
                    count++;
                }

                // Use the count to add the new record from the user.
                ResultSet carInsertRs = stmt.executeQuery("INSERT INTO week5cars(carid, car) VALUES ('" + 
                        count + "','" + car + "')");

                // Get the data set again that now includes the users entry.
                ResultSet finalRs = stmt.executeQuery("SELECT * FROM WEEK5CARS ORDER BY CARID ASC");

                // Loop through the list to insert items into the table. Make sure to open the new
                // row and close within the query as long as a new item from table exists.
                while(finalRs.next()){
                    out.println("<tr>");
                    out.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    out.println(finalRs.getString(index));
                    out.println("</td>");

                    // Increase the index to grab the next piece.
                    index++;

                    out.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    out.println(finalRs.getString(index));
                    out.println("</td>");
                    out.println("</tr>");

                    // Descrease the index to support the next row.
                    index--;
                }
                stmt.close();
		    	con.close();
                
            }
            else if(color.isEmpty() && car.isEmpty()){
                // Get the data from the table and to find the final row number; for primary key.
                Statement stmt = con.createStatement();
                ResultSet bikeRs = stmt.executeQuery("SELECT * FROM WEEK5BIKES");
                int count = 1;

                // Set count by counting all records fetched.
                while(bikeRs.next()){
                    count++;
                }

                // Use the count to add the new record from the user.
                ResultSet bikeInsertRs = stmt.executeQuery("INSERT INTO WEEK5BIKES(bikeid, bike) VALUES ('" + 
                        count + "','" + bike + "')");

                // Get the data set again that now includes the users entry.
                ResultSet finalRs = stmt.executeQuery("SELECT * FROM WEEK5BIKES ORDER BY BIKEID ASC");

                // Loop through the list to insert items into the table. Make sure to open the new
                // row and close within the query as long as a new item from table exists.
                while(finalRs.next()){
                    out.println("<tr>");
                    out.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    out.println(finalRs.getString(index));
                    out.println("</td>");

                    // Increase the index to grab the next piece.
                    index++;

                    out.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    out.println(finalRs.getString(index));
                    out.println("</td>");
                    out.println("</tr>");

                    // Descrease the index to support the next row.
                    index--;
                }
                stmt.close();
		    	con.close();
            }
            else if(car.isEmpty() && bike.isEmpty()){
                // Get the data from the table and to find the final row number; for primary key.
                Statement stmt = con.createStatement();
                ResultSet colorRs = stmt.executeQuery("SELECT * FROM WEEK5COLORS");
                int count = 1;

                // Set count by counting all records fetched.
                while(colorRs.next()){
                    count++;
                }

                // Use the count to add the new record from the user.
                ResultSet colorInsertRs = stmt.executeQuery("INSERT INTO WEEK5COLORS(colorid, color) VALUES ('" + 
                        count + "','" + color + "')");

                // Get the data set again that now includes the users entry.
                ResultSet finalRs = stmt.executeQuery("SELECT * FROM WEEK5COLORS ORDER BY COLORID ASC");

                // Loop through the list to insert items into the table. Make sure to open the new
                // row and close within the query as long as a new item from table exists.
                while(finalRs.next()){
                    out.println("<tr>");
                    out.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    out.println(finalRs.getString(index));
                    out.println("</td>");

                    // Increase the index to grab the next piece.
                    index++;

                    out.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    out.println(finalRs.getString(index));
                    out.println("</td>");
                    out.println("</tr>");

                    // Descrease the index to support the next row.
                    index--;

                }
                stmt.close();
		    	con.close();
            }
            else{
                out.println("Nothing submitted");
            }

		}
		catch (java.lang.Exception ex){

			ex.printStackTrace();
        }

        printFooter(out);
    }

    public void printHeader(PrintWriter out) {

        out.println("<html>");
        out.println("<head>");
        out.println("<title>");
        out.println("Form Post Servlet 2");
        out.println("</title>");
        out.println("<link rel='stylesheet' type='text/css' href='styles.css'>");
        out.println("</head>");
        out.println("<body>");
        out.println("<h1>");
        out.println("Josh Greenert's Form Post Servlet 2");
        out.println("</h1>");
    }

    public void printFooter(PrintWriter out) {

        out.println("</div>");
        out.println("</table>");
        out.println("</body>");
        out.println("</html>");
    }

    public void printForms1(PrintWriter out) {

        // First form for the day of the week.
        out.println("<form method='post' action='http://" + target + "/Database_Data'>");
        out.println("<label>Enter A New Car</label>   ");
        out.println("<input type='text' name='week5cars' size='40' maxlength='40'/>");
        out.println("<input type='submit' />");
        out.println("</form>");
        out.println("<br>");

        // Second form for the user's first thought.
        out.println("<form method='post' action='http://" + target + "/Database_Data'>");
        out.println("<label>Enter A New Bike</label>   ");
        out.println("<input type='text' name='week5bikes' size='40' maxlength='40'/>");
        out.println("<input type='submit' />");
        out.println("</form>");
        out.println("<br>");

        // Third form for the user's favorite word.
        out.println("<form method='post' action='http://" + target + "/Database_Data'>");
        out.println("<label>Enter A New Color</label>   ");
        out.println("<input type='text' name='week5colors' size='40' maxlength='40'/>");
        out.println("<input type='submit' />");
        out.println("</form>");
        out.println("<br>");
        
    }
}