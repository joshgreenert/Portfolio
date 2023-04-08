
package customtags;

import javax.servlet.jsp.tagext.*;
import javax.servlet.jsp.*;
import java.io.*;

public class customtag extends SimpleTagSupport {


   public void doTag() throws JspException, IOException {

      JspWriter outObject = getJspContext().getOut();
      // Form for the three fields to enter intot the database.
      outObject.println("<div class='form' style='text-align: center;'>");
      outObject.println("<form method=\'post' action=\'http://localhost:7070/joshgreenertWeek8/joshgreenertFormPost5'>");
      outObject.println("<h1>Enter A New Car</h1>   ");
      outObject.println("<input type='text' name='car' id='car' size='40' placeholder='Manufacturer' maxlength='40'/><br>");
      outObject.println("<input type='text' name='model' id='model' size='40' placeholder='Model' maxlength='40'/><br>");
      outObject.println("<input type='text' name='price' id='price' size='40' placeholder='Price' maxlength='40'/><br>");
      outObject.println("<input type='submit' />");
      outObject.println("</form>");
      outObject.println("</div>");
      outObject.println("<br>");
   }
}