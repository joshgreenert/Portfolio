/*
 * Created by Joshua Greenert  9/13/2020  Bellevue University  Assignment 4.1
 * 
 * This servlet will accept information from the user to create a random access file
 * and modify it's contents.  After the user presses the submit button, the contents
 * of the file will be added to the servlet to be displayed.  
 * 
 * NOTE: There are three forms needed and the file must be saved in the TEMP directory.
 */


import javax.servlet.*;
import javax.servlet.http.*;
import java.io.*;

public class JoshGreenertFormPost extends HttpServlet {

    // Specify the target host that is the servlet.
    private String target = "localhost:7070/Week_4";

    // Add variables to set up the Java file.
    String path = "C:\\temp\\";
    String filename = path + "joshgreenertweek4.dat";
    String day = "";
    String firThought = "";
    String word = "";
    String contents = "";
    String space = " ";

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
        String day = (request.getParameter("dayOfWeek") != null) ? request.getParameter("dayOfWeek") : "";
        String firThought = (request.getParameter("firstThought") != null) ? request.getParameter("firstThought") : "";
        String word = (request.getParameter("favWord") != null) ? request.getParameter("favWord") : "";

        // Create the random access file and send the users input to the file.
        try{

            StringBuffer buffer = new StringBuffer();
            RandomAccessFile raf = new RandomAccessFile(filename, "rw");

            // Set the seek to the end of the file.
            raf.seek(raf.length());

            // Write each form field to the file as needed.
            raf.write(day.getBytes());
            raf.write(space.getBytes());
            raf.write(firThought.getBytes());
            raf.write(space.getBytes());
            raf.write(word.getBytes());
            raf.write(space.getBytes());

            // Set the contents to the beginning to read the variables.
            raf.seek(0);
            
            // Read the contents of the RAF and send them to the screen.
            while(raf.getFilePointer() < raf.length()) {
                buffer.append(raf.readLine()+System.lineSeparator());
            }
            contents = buffer.toString();

            raf.close();

        }
        catch(IOException e){
            e.printStackTrace();
        }
        out.println(day + " " +  firThought + " " +word);
        out.println(contents);

        printFooter(out);
    }

    public void printHeader(PrintWriter out) {

        out.println("<html>");
        out.println("<head>");
        out.println("<title>");
        out.println("Form Post Servlet");
        out.println("</title>");
        out.println("</head>");
        out.println("<body>");
        out.println("<h1>");
        out.println("Josh Greenert's Form Post Servlet");
        out.println("</h1>");
        out.println("<div>");
    }

    public void printFooter(PrintWriter out) {

        out.println("</div>");
        out.println("</body>");
        out.println("</html>");
    }

    public void printForms1(PrintWriter out) {

        // First form for the day of the week.
        out.println("<form method='post' action='http://" + target + "/Show_Data'>");
        out.println("<label>Enter Day of Week</label>   ");
        out.println("<input type='text' name='dayOfWeek' size='40' maxlength='40'/>");
        out.println("<input type='submit' />");
        out.println("</form>");
        out.println("<br>");

        // Second form for the user's first thought.
        out.println("<form method='post' action='http://" + target + "/Show_Data'>");
        out.println("<label>Enter Your First Thought</label>   ");
        out.println("<input type='text' name='firstThought' size='40' maxlength='40'/>");
        out.println("<input type='submit' />");
        out.println("</form>");
        out.println("<br>");

        // Third form for the user's favorite word.
        out.println("<form method='post' action='http://" + target + "/Show_Data'>");
        out.println("<label>Enter Your Favorite Word</label>   ");
        out.println("<input type='text' name='favWord' size='40' maxlength='40'/>");
        out.println("<input type='submit' />");
        out.println("</form>");
        out.println("<br>");
        
    }
}