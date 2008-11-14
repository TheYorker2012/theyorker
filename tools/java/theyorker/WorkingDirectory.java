package theyorker;

import java.awt.Component;
import java.awt.Frame;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.beans.PropertyChangeEvent;
import java.beans.PropertyChangeListener;
import java.io.File;
import java.net.MalformedURLException;
import java.util.prefs.Preferences;

import javax.swing.JButton;
import javax.swing.JDialog;
import javax.swing.JFileChooser;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextField;

public class WorkingDirectory {
	String WD;
	
	private void SaveWorkingDirectory(){
		Preferences prefs = Preferences.userNodeForPackage(this.getClass());
		prefs.put("WD", WD);
	}
	
	private Boolean LoadWorkingDirectory(){
		Preferences prefs = Preferences.userNodeForPackage(this.getClass());
		String TempWD =  prefs.get("WD", null);
		if(TempWD==null){
			return false;
		}else{
			WD=TempWD;
			return true;
		}
	}
	
	public WorkingDirectory(Component Parent){
		Constructor();
		if(!LoadWorkingDirectory()){
			WD = getBestGuess();
			UserPrompt(Parent);
			SaveWorkingDirectory();
		}
	}
	
	public WorkingDirectory(String NewWD) throws MalformedURLException{
		Constructor();
		WD = NewWD;
		SaveWorkingDirectory();
	}
	
	private String getBestGuess(){
		return System.getProperty("user.home")
						+File.separator
						+ ".yphoto"
						+ File.separator;
//TODO --Try and use JNI to get correct directories?		
//		if(System.getProperty("os.name").toUpperCase().contains("WINDOWS")){
//		
//		}else if(System.getProperty("os.name").toUpperCase().contains("MAC")){
//
//		}else{ // Assume *nix
//			
//		}
	}
	
	public void UserPrompt(Component Parent){
		JPanel Panel = new JPanel(new GridBagLayout());
		GridBagConstraints con = new GridBagConstraints();
		con.gridx = 0;
		con.gridy = 0;
		con.gridwidth = GridBagConstraints.REMAINDER;
		con.gridheight = 1;
		JLabel msg = new JLabel(
				"<html>" +
				"Please choose a working directory. This will <br>" +
				"be where photo files will be temporarily stored.<br>" +
				"If in doubt the default should be sufficient." +
				"</html>");
		Panel.add(msg,con);
		JTextField LocationField = new JTextField(20);
		LocationField.setText(WD);
		con.gridx = 0;
		con.gridy = 1;
		con.gridwidth = 1;
		con.gridheight = 1;	
		Panel.add(LocationField);
		JButton Chooser = new JButton("...");
		Chooser.setActionCommand("Show FolderChooser");
		FolderChooserListener listen = new FolderChooserListener(Parent,LocationField);
		Chooser.addActionListener(listen);
		con.gridx = 1;
		con.gridy = 1;
		con.gridwidth = 1;
		con.gridheight = 1;
		Panel.add(Chooser);
		JOptionPane OptionPane = new JOptionPane(Panel,JOptionPane.PLAIN_MESSAGE);
		JDialog Dialog = new JDialog((Frame) Parent,"Please Choose a Working Directory",true);
		Dialog.setContentPane(OptionPane);
		Dialog.setDefaultCloseOperation(
					JDialog.DO_NOTHING_ON_CLOSE);
		OptionPane.addPropertyChangeListener(JOptionPane.VALUE_PROPERTY,
				new DialogCloseListener(
								LocationField,Dialog,Parent));

		Dialog.pack();
		Dialog.setLocationByPlatform(true);
		Dialog.setVisible(true);
		WD = LocationField.getText();
	}
	
	private void Constructor(){
	}

	private class DialogCloseListener implements PropertyChangeListener{
		JTextField LocationField;
		JDialog Dialog;
		Component Parent;
		
		public DialogCloseListener(JTextField T,JDialog D,Component P){
			LocationField = T;
			Dialog = D;
			Parent = P;
		}
		
		
		
		public void propertyChange(PropertyChangeEvent e) {
			System.out.print(e.getPropertyName()+"\n"+JOptionPane.VALUE_PROPERTY+"\n--------\n");
			if (Dialog.isVisible() &&
					e.getPropertyName().equals(JOptionPane.VALUE_PROPERTY) &&
					e.getNewValue().equals(new Integer(JOptionPane.OK_OPTION))){
				File TheFile = new File(LocationField.getText());
				if (TheFile.isDirectory()){
					if(TheFile.listFiles().length==0){
						Dialog.setVisible(false);
					}else{
						if (JOptionPane.showOptionDialog(
								Parent,
								"Non-Enpty Directory Chosen. Continue?",
								"Invalid Working Directory",
								JOptionPane.YES_NO_OPTION,
								JOptionPane.ERROR_MESSAGE,
								null, null, null)==JOptionPane.YES_OPTION){
							Dialog.setVisible(false);
						}else{ResetPane();}
					}
				}else{
					if(TheFile.mkdirs()){
						Dialog.setVisible(false);
					}else{
						JOptionPane.showMessageDialog(
								Parent,
								"Unable to create the chosen directory " +
								"structure. Please recheck.  Some directories " +
								"may have been created in the previous step.",
								"Error Creating Directories",
								JOptionPane.ERROR_MESSAGE);
						ResetPane();
					}
				}
			}
		}
		
		private void ResetPane(){
			((JOptionPane) Dialog.getContentPane()).setValue(JOptionPane.UNINITIALIZED_VALUE);
		}
		
	}
	
	private class FolderChooserListener implements ActionListener{
			Component Parent;
			JTextField LocationField;
			
			public FolderChooserListener(Component P, JTextField T){
				Parent =P;
				LocationField = T;
			}

			public void actionPerformed(ActionEvent e) {
			if(e.getActionCommand().equals("Show FolderChooser")){
				JFileChooser fc = new JFileChooser();
			
				fc.setFileSelectionMode(JFileChooser.DIRECTORIES_ONLY);
				if(fc.showOpenDialog(Parent) == JFileChooser.APPROVE_OPTION){
					LocationField.setText(fc.getSelectedFile().getAbsolutePath());
				}
			}
		}
	}

	public String getString() {
		return WD;
	}
}
