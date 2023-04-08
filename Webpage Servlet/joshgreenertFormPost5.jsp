<%--
/*
 * Created by Joshua Greenert  10/10/2020  Bellevue University  Assignment 8.1
 * 
 * This JSP file will submit a form post request to the database using a Java Bean and a custom
 * tag field.  After the data has been submitted and the JSP is redirected to the same JSP, the
 * entire table will be displayed to the user.
 * 
 * TABLE: week5cars
 */
--%>

<%@ page import="javax.servlet.*,javax.servlet.http.*,java.io.*,java.sql.*,java.util.*" %>
<!DOCTYPE html>
<jsp:useBean id='car' class='coffeebean.MyFirstBean'  />
<%@ taglib prefix = 'c' uri='WEB-INF/customtag.tld'%>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel='stylesheet' type='text/css' href='styles.css'>
    <title>JSP With Java Bean And Custom Tag</title>
</head>
<body>
<%!
public void printForms1(PrintWriter outObject) {
    
    try{
        
%>

        <c:date/>

<%!

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
        <jsp:setProperty name='car' property='model' value='${param.model}' />
        <jsp:setProperty name='car' property='price' value='${param.price}' />
<%!

        // Add variables to set up the Java file.
        String car = "";
        String model = "";
        int price = 0;


        // Start the table where the data will be filled into.
        outObject.println("<div class='tablecss'>");
        outObject.println("<h2 style='text-align:center;'>Data From Query</h2>");
        outObject.println("<table style='border: 1px solid black; margin-left: auto; margin-right: auto; background:white;'");
        outObject.println("<tr>");
        outObject.println("<th style='text-align:center; border: 1px solid #000000;'>ID</th>");
        outObject.println("<th style='text-align:center; border: 1px solid #000000;'>CAR</th>");
        outObject.println("<th style='text-align:center; border: 1px solid #000000;'>MODEL</th>");
        outObject.println("<th style='text-align:center; border: 1px solid #000000;'>PRICE</th>");
        outObject.println("</tr>");
        outObject.println("<tr>");

        // Start up the MySQL database and get the data.
        try{

			// Create an index to create a start point.
			int index = 1;
            
            // Create a connection to use for statements.
			DriverManager.registerDriver (new oracle.jdbc.OracleDriver());
			Connection con = DriverManager.getConnection("jdbc:oracle:thin:@localhost", "joshgreenert1","iamsilly2");

            car = request.getParameter("car");
            model = request.getParameter("model");
            price = Integer.valueOf(request.getParameter("price"));
            
            // Check which variable the user entered for which form was posted.
            if(!(car.equals("")) && !(model.equals("")) && price != 0){
                
                // Get the data from the table and to find the final row number; for primary key.
                Statement stmt = con.createStatement();
                ResultSet carRs = stmt.executeQuery("SELECT * FROM WEEK5CARS");
                int count = 1;

                // Set count by counting all records fetched.
                while(carRs.next()){
                    count++;
                }

                // Use the count to add the new record from the user.
                ResultSet carInsertRs = stmt.executeQuery("INSERT INTO week5cars(carid, car, make, price) VALUES ('" + 
                        count + "','" + car + "','" + model + "','" + price +"')");

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

                    // Increase the index to grab the next piece.
                    index++;

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
                    index--;
                    index--;

                }
                stmt.close();
		    	con.close();
                
            }
            else{
            
                outObject.println("<p>Nothing Submitted</p>");
                
            }

		}
		catch (java.lang.Exception ex){

			ex.printStackTrace();
        }   
}
%>
    <p style="display: none;">
    <jsp:getProperty name='car' property='car' />
    <jsp:getProperty name='car' property='model' />
    <jsp:getProperty name='car' property='price' />
    </p>
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