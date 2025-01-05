package de.htwsaar.cantineplanner.presentation;

public class CLI {
    private final ProgrammHelper myhelper ;

    public CLI() {
            myhelper = new ProgrammHelper();
    }
    public int chooseAction() {
        System.out.println("===== Action MENU =====");
        System.out.println(" Choose: "  );
        System.out.println(" 1.Add new dish");
        System.out.println(" 2.Open statistics");
        return myhelper.promptNumber("");
    }

}
