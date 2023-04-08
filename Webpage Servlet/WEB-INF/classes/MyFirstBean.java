package coffeebean;

/*
 *
 * Place me in the classes directory under the WEB-INF directory
 * of the application directory
 *
 * Compile using:
 * javac -d . MyFirstBean.java
 */


// Declare the class that will be our bean
public class MyFirstBean {

    // Declare our properties
    private String car = "Toyota";
    private String model = "corolla";
    private int price = 5;
    
    //Must have default constructor either one you provide or the default constructor Java provides
    public MyFirstBean() {
        this.car = car;
        this.model = model;
        this.price = price;
    }

    // Getters and Setters
    public void setCar(String car) {
        this.car = car;
    }
    public void setModel(String model) {
        this.model = model;
    }
    public void setPrice(int price) {
        this.price = price;
    }

    public String getCar() {
        return this.car;
    }
    public String getModel() {
        return this.model;
    }
    public int getPrice() {
        return this.price;
    }
}