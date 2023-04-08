
/*
 * Created by Joshua Greenert 9/7/2020 Assignment 3.1  Bellevue University
 * 
 * This program will provide a conversion of an ASCI file from the C:/temp directory
 * and display it to the user.  
 */
import javax.servlet.*;
import javax.servlet.http.*;
import java.io.*;
import java.util.*;

public class TestingServlet extends HttpServlet {

	// 
	public void doGet(HttpServletRequest request, HttpServletResponse response)
	throws ServletException, IOException{

		PrintWriter out = response.getWriter();
		printHeader(out);
		out.println("<div><h1>ASCI Servlet Display</h1></div>");
		printForm(out);
	}

	public void doPost(HttpServletRequest request, HttpServletResponse response)
	throws ServletException, IOException{

		PrintWriter out = response.getWriter();
		printHeader(out);
		out.println(request.getParameter("textarea"));
		out.println("<br>");
	}

	// Print header to call for the doPost and doGet methods.  
	// Information here is general.
	public void printHeader(PrintWriter out){

	out.println("<!DOCTYPE html>");
	out.println("<html lang='en'>");
	out.println("<html>");
	out.println("<head>");
	out.println("<title>");
	out.println("ASCI Servlet");
	out.println("</title>");
	out.println("<meta charset='utf-8'>");
	out.println("</head>");
	out.println("<body>");
	out.println("<div>");
	}

	// Create a textarea element to hold the text that will be displayed from the ASCI file.
	public void printForm(PrintWriter out){

	out.println("<p>");

	// Create a variable to hold the text that will be placed into the textarea.
	String text = "";

	// Get the file data and store it into the text variable to display.
	try{
		// Set the file name and path to ensure pulling from the correct location.
		String path = "C:\\temp\\";
		String filename = path + "servlet1.dat";
		int index;

		FileReader asciReader = new FileReader(filename);

		// While loop to get all of the data.
		while((index = asciReader.read()) != -1){
			text += (char)index;
		}
		
		// Close the reader object.
		asciReader.close();
	}
	catch(IOException e){
		e.printStackTrace();
	}

	// Now that the text is populated with the asci characters, convert them to an array
	// and convert each set of numbers to a word.  Then split the words by spaces into a string.
	String[] arrayOfStr = text.split(" ");
	text = "";

	for(String i : arrayOfStr){
		text += Character.toString((char)Integer.parseInt(i)) + " ";
	}

	// Add the textarea field and place the text into it.
	out.println("<textarea name='textarea' rows='20' cols='40'>");
	out.println(text);
	out.println("</textarea>");

	// Close the tags leftover: p, div, body, html.
	out.println("</p>");
	out.println("</div>");
	out.println("</body>");
	out.println("</html>");
	}
 
}
