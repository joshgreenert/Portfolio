<%--
/*
 * Created by Joshua Greenert  10/4/2020  Bellevue University  Assignment 7.1
 * 
 * This JSP file will submit a form post request to the database using a Java Bean.  Afterwards,
 * the data submitted will be displayed from the database to the user.
 * 
 * TABLES: week5cars, week5bikes, week5colors
 */
--%>

<%@ page import="javax.servlet.*,javax.servlet.http.*,java.io.*,java.sql.*" %>
<!DOCTYPE html>
<jsp:useBean id='car' class='coffeebean.MyFirstBean'  />
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel='stylesheet' type='text/css' href='styles.css'>
    <title>JSP With Java Bean</title>
</head>
<body>

<%!
public void printForms1(PrintWriter outObject) {
    
    try{
        // First form for the day of the week.
        outObject.println("<form method=\'post' action=\'http://localhost:7070/joshgreenertWeek7/joshgreenertFormPost4'>");
        outObject.println("<label>Enter A New Car</label>   ");
        outObject.println("<input type='text' name='car' size='40' maxlength='40'/>");
        outObject.println("<input type='submit' />");
        outObject.println("</form>");
        outObject.println("<br>");

    }catch(Exception e){
        e.printStackTrace();
    }
}

public void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {

        PrintWriter outObject = response.getWriter();
        printForms1(outObject);

}

public void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {

        PrintWriter outObject = response.getWriter();
        printForms1( outObject);

%>
        <jsp:setProperty name='car' property='car' value='${param.car}' />
<%!

        // Add variables to set up the Java file.
        String car = "";

        // Get the data from the form button by using ternary statements to account for null values.
        car = (request.getParameter("week5cars") != null) ? request.getParameter("week5cars") : "";

        // Start the table where the data will be filled into.
        outObject.println("<div class='tablecss'>");
        outObject.println("<h2 style='text-align:center;'>Data From Query</h2>");
        outObject.println("<table style='border: 1px solid black; margin-left: auto; margin-right: auto; background:white;'");
        outObject.println("<tr>");
        outObject.println("<th style='text-align:center; border: 1px solid #000000;'>ID</th>");
        outObject.println("<th style='text-align:center; border: 1px solid #000000;'>ITEM</th>");
        outObject.println("</tr>");
        outObject.println("<tr>");

        // Start up the MySQL database and get the data.
        try{

			// Create an index to create a start point.
			int index = 1;
            
            // Create a connection to use for statements.
			DriverManager.registerDriver (new oracle.jdbc.OracleDriver());
			Connection con = DriverManager.getConnection("jdbc:oracle:thin:@localhost", "joshgreenert1","iamsilly1");

            car = request.getParameter("car");

            // Check which variable the user entered for which form was posted.
            if(!(car.isEmpty())){
                
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

                    outObject.println("<tr>");
                    outObject.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    outObject.println(finalRs.getString(index));
                    outObject.println("</td>");

                    // Increase the index to grab the next piece.
                    index++;

                    outObject.println("<td style='text-align:center; border: 1px solid #000000;'>");
                    outObject.println(finalRs.getString(index));
                    outObject.println("</td>");
                    outObject.println("</tr>");

                    // Descrease the index to support the next row.
                    index--;
                }
                stmt.close();
		    	con.close();
                
            }
            else{
            
                outObject.println("Nothing Submitted");
                
            }

		}
		catch (java.lang.Exception ex){

			ex.printStackTrace();
        }   
}
%>
<p><strong>CAR: </strong><jsp:getProperty name='car' property='car' /><br></p>
<%
if(request.getMethod().equals("GET")){
    
        doGet( request, response);
    
%>

<%
}
if(request.getMethod().equals("POST")){

    doPost( request, response);

}
%>


</body>

</html>