
import javax.servlet.*;
import javax.servlet.http.*;
import java.io.*;

public class FormPostGet extends HttpServlet {

    private String target = "localhost:7070";

    public void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {

        PrintWriter out = response.getWriter();

        printHeader(out);
        printForm(out);
        printFooter(out);
    }

    public void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {

        PrintWriter out = response.getWriter();

        printHeader(out);
        printForm(out);
        out.println(request.getParameter("myName"));
        printFooter(out);
    }

    public void printHeader(PrintWriter out) {

        out.println("<html>");
        out.println("<head>");
        out.println("<title>");
        out.println("Form Post & Get");
        out.println("</title>");
        out.println("</head>");
        out.println("<body>");
        out.println("<div>");
    }

    public void printFooter(PrintWriter out) {

        out.println("</div>");
        out.println("</body>");
        out.println("</html>");
    }

    public void printForm(PrintWriter out) {

        out.println("<form method='post' action='http://" + target + "/Week_04/servlet/classes/FormPostGet'>");
        out.println("<label>Enter Name</label>   ");
        out.println("<input type='text' name='myName' size='40' maxlength='40'/>");
        out.println("<input type='submit' />");
        out.println("</form>");
    }
}