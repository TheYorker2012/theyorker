package theyorker;

import java.awt.Dimension;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyEvent;
import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.util.LinkedList;
import java.util.ListIterator;

import javax.swing.*;
import javax.swing.event.ListSelectionEvent;
import javax.swing.event.ListSelectionListener;

/**
 * @author David
 *
 */
public class yphoto extends JFrame 
				implements ActionListener,ListSelectionListener{

	private static final long serialVersionUID = 1L;

	private WorkingDirectory WorkingDir;
	private XMLHandler XML;
	private JFileChooser fc;
	private PhotoList ThePhotoList;
	private JComponent PhotoDetails;
	private JSplitPane Body;
	private JTextField PhotographerField;
	private String Tags[];
	
	public yphoto(){
		WorkingDir = new WorkingDirectory(this);
		XML = new XMLHandler(new File(WorkingDir.getString()+File.separator+"yphoto.xml"));
		setSize(720,700);
		setTitle("The Yorker Photo Upload Utility");
		setLocationRelativeTo(null);
		setLayout(new java.awt.BorderLayout());
		setDefaultCloseOperation(WindowConstants.EXIT_ON_CLOSE);
		// ready File Chooser
			fc = new JFileChooser();
			fc.setFileFilter(new ImageFilter());
			fc.setAcceptAllFileFilterUsed(false);
			fc.setMultiSelectionEnabled(true);
		// ToolBar
			JToolBar Toolbar = new JToolBar();
			add(Toolbar,java.awt.BorderLayout.PAGE_START);
			JPanel UnderToolBar = new JPanel(new java.awt.BorderLayout());
			add(UnderToolBar,java.awt.BorderLayout.CENTER);
		//ToolBar Buttons
			//Open Button
				JButton Open = new JButton("Open");
				Open.setMnemonic(KeyEvent.VK_O);
				Open.setActionCommand("Open");
				Open.setToolTipText("Click to Add Images");
				Open.addActionListener(this);
				Toolbar.add(Open);
			//Delete Button
				JButton Delete = new JButton("Delete");
				Delete.setMnemonic(KeyEvent.VK_D);
				Delete.setActionCommand("Delete");
				Delete.setToolTipText("Click to Delete Currently Selected image");
				Delete.addActionListener(this);
				Toolbar.add(Delete);
			//Update Tags Button
				JButton UpdTags = new JButton("Update Tag List");
				UpdTags.setActionCommand("Update Tags");
				UpdTags.setToolTipText("Download list of current tags from theyorker.co.uk, if a connection is possible");
				UpdTags.addActionListener(this);
				Toolbar.add(UpdTags);
			//Upload Button
				JButton UploadBtn = new JButton("Upload");
				UploadBtn.setActionCommand("Upload");
				UploadBtn.addActionListener(this);
				Toolbar.add(UploadBtn);
			//Save Button
				JButton SaveBtn = new JButton("Save");
				SaveBtn.setActionCommand("Save");
				SaveBtn.addActionListener(this);
				Toolbar.add(SaveBtn);
			//Glue
				Toolbar.add(Box.createGlue());
			//Default Photographer
				JLabel PhotographerLabel = new JLabel("Default Photographer:");
				PhotographerLabel.setBorder(BorderFactory.createEmptyBorder(0,10,0,0));
				Toolbar.add(PhotographerLabel);
				PhotographerField = new JTextField(25);
				PhotographerField.setMaximumSize(new Dimension(200, 50));
				Toolbar.add(PhotographerField);
			
		// Header Bar
			JLabel HeaderBar = new JLabel();
			HeaderBar.setIcon(new ImageIcon((java.net.URL) yphoto.class.getResource("bar.gif")));
			UnderToolBar.add(HeaderBar,java.awt.BorderLayout.PAGE_START);
		// Page Body
			ThePhotoList = new PhotoList();
			ThePhotoList.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
			ThePhotoList.setLayoutOrientation(PhotoList.VERTICAL);
			ThePhotoList.setVisibleRowCount(-1);
			ThePhotoList.setCellRenderer(new PhotoListRenderer());
			ThePhotoList.addListSelectionListener(this);
			PhotoDetails = new JPanel();
			JScrollPane ScrollList =new JScrollPane(ThePhotoList,
							JScrollPane.VERTICAL_SCROLLBAR_AS_NEEDED,
							JScrollPane.HORIZONTAL_SCROLLBAR_NEVER);
			ScrollList.setMinimumSize(new Dimension(100,100));
			Body = new JSplitPane(JSplitPane.HORIZONTAL_SPLIT,
									ScrollList,
									PhotoDetails);
			Body.setDividerLocation(200);
			UnderToolBar.add(Body,java.awt.BorderLayout.CENTER);
		
		// Try to update Tags (in new thread so does not block)
			(new TagUpdateThread()).start();
		// DISPLAY!
			setVisible(true);
	}

	public void actionPerformed(ActionEvent Event) {
		String Command = Event.getActionCommand();
		if(Command.equals("Open")){
			AddFiles();
		}else if(Command.equals("Delete")){
			ThePhotoList.DeleteCurrentPhoto();
		}else if(Command.equals("Update Tags")){
			UpdateTags(true);
		}else if(Command.equals("Photo Delete Tag")){
			((PhotoPanel)PhotoDetails).DeleteCurrentTag();
		}else if(Command.equals("Photo Add Tag")){
			String Tag = GetTag();
			if(Tag != null){
				((PhotoPanel)PhotoDetails).addTag(Tag);
			}
		}else if(Command.equals("Upload")){
			Upload();
		}else if(Command.equals("Save")){
			Save();
		}
	}
	
	private void Save(){
		try {
			XML.UpdateAndSave(Tags,ThePhotoList);
		} catch (Exception e) {
			JOptionPane.showMessageDialog(this, e.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
		}
	}
	
	private HttpURLConnection HTTPConnect(URL url,boolean DoIn, boolean DoOut) throws IOException{
		HttpURLConnection connection = (HttpURLConnection) url.openConnection();
		connection.setUseCaches(false);
		connection.setDoInput(DoIn);
		connection.setDoOutput(DoOut);
		return connection;
	}
	
	private void SetCookies(LinkedList<String> CookieList,HttpURLConnection connection){
		ListIterator<String> CIt = CookieList.listIterator();
		
		while(CIt.hasNext()){
			connection.setRequestProperty("Cookie", CIt.next());
		}
	}
	
	private void AddCookies(LinkedList<String> CookieList,HttpURLConnection connection){
		int i=1;
		while (connection.getHeaderFieldKey(i) != null ){
			if(connection.getHeaderFieldKey(i).equalsIgnoreCase("set-cookie")){
				CookieList.add(connection.getHeaderField(i));
			}
			i++;
		}
		
	}
	
	private void Upload(){
		if(ThePhotoList.isEmpty()){return;}
		
		try{
			LinkedList<String> CookieList = new LinkedList<String>();
			
			URL url = new URL("http://localhost:8888/yphoto/upload");
			HttpURLConnection.setFollowRedirects(false);
			HttpURLConnection connection =  HTTPConnect(url,true,false);
			connection.getInputStream();
			// traverses redirects, but updates url
			int i=1;
			while (connection.getHeaderFieldKey(i) != null ){
				if(connection.getHeaderFieldKey(i).equalsIgnoreCase("location")){
					url = new URL("http://localhost:8888"+ connection.getHeaderField(i));
					connection =  HTTPConnect(url,true,false);
					connection.getInputStream();
					i = 1;
				} else {
					i++;
				}
			}
			
			if(connection.getResponseCode() != 200){throw new Exception();}
			AddCookies(CookieList,connection);
			
			connection = HTTPConnect(url,true,true);
			String query="username="+URLEncoder.encode("dg516","UTF-8");
			query+="&";
			query+="password="+URLEncoder.encode("wohabet59","UTF-8");
			query+="&login_button=Login";
			query+="&login_id=student";
			connection.setRequestMethod("POST");
			
			connection.setRequestProperty("Content-length",String.valueOf (query.length())); 
			connection.setRequestProperty("Content-Type","application/x-www-form-urlencoded");
			SetCookies(CookieList,connection);
			DataOutputStream output = new DataOutputStream(connection.getOutputStream());
			output.writeBytes(query);
			output.flush();
			output.close();
			connection.getInputStream();
			AddCookies(CookieList,connection);
			i=1;
			while (connection.getHeaderFieldKey(i) != null ){
				if(connection.getHeaderFieldKey(i).equalsIgnoreCase("location")){
					url = new URL("http://localhost:8888"+ connection.getHeaderField(i));
					connection =  HTTPConnect(url,true,false);
					SetCookies(CookieList,connection);
					connection.getInputStream();
					i = 1;
				} else {
					i++;
				}
			}
			
			connection = HTTPConnect(url,true,true);
			query+="password="+URLEncoder.encode("wohabet59","UTF-8");
			query+="&login_button=Login";
			query+="&login_id=office";
			connection.setRequestMethod("POST");
			
			connection.setRequestProperty("Content-length",String.valueOf (query.length())); 
			connection.setRequestProperty("Content-Type","application/x-www-form-urlencoded");
			SetCookies(CookieList,connection);
			output = new DataOutputStream(connection.getOutputStream());
			output.writeBytes(query);
			output.flush();
			output.close();
			InputStreamReader input = new InputStreamReader(connection.getInputStream());
			AddCookies(CookieList,connection);
			i=1;
			while (connection.getHeaderFieldKey(i) != null ){
				if(connection.getHeaderFieldKey(i).equalsIgnoreCase("location")){
					url = new URL("http://localhost:8888"+ connection.getHeaderField(i));
					connection =  HTTPConnect(url,true,false);
					SetCookies(CookieList,connection);
					input = new InputStreamReader(connection.getInputStream());
					i = 1;
				} else {
					i++;
				}
			}
			
			if(!(new BufferedReader(input)).readLine().equalsIgnoreCase("ready")){
				throw new Exception();
			}
			
			while(!ThePhotoList.isEmpty()){
				String boundary = "-------------------" +
									Long.toString(System.currentTimeMillis(),16);
				Photo CurrentPhoto = ThePhotoList.GetCurrentPhoto();
				connection = HTTPConnect(url,true,true);
				connection.setRequestMethod("POST");
				connection.setUseCaches(false);
				SetCookies(CookieList,connection);
				connection.setRequestProperty("Connection", "Keep-Alive");
				connection.setRequestProperty("Content-Type","multipart/form-data; boundary="+ boundary);
				output = new DataOutputStream(connection.getOutputStream());
				output.writeBytes("--"+boundary+"\r\n");
				output.writeBytes("Content-Disposition: form-data; name=\"photographer\"\r\n\r\n");
				output.writeBytes(CurrentPhoto.getPhotographer()+"\r\n");
				output.writeBytes("--"+boundary+"\r\n");
				output.writeBytes("Content-Disposition: form-data; name=\"title\"\r\n\r\n");
				output.writeBytes(CurrentPhoto.GetTitle()+"\r\n");
				output.writeBytes("--"+boundary+"\r\n");
				output.writeBytes("Content-Disposition: form-data; name=\"tags\"\r\n\r\n");
				output.writeBytes(CurrentPhoto.getTagString()+"\r\n");
				output.writeBytes("--"+boundary+"\r\n");
				output.writeBytes("Content-Disposition: form-data; name=\"photo\"; filename=\""+CurrentPhoto.getFileName()+"\"\r\n");
				output.writeBytes("Content-Type: image/jpeg\r\n\r\n");
				BufferedInputStream PhotoInput = CurrentPhoto.getFile();
				byte[] data = new byte[1024];
				int r = 0;
				while((r = PhotoInput.read(data, 0, data.length)) != -1) {
					output.write(data, 0, r);
				}
				PhotoInput.close();
				output.writeBytes("\r\n");
				output.writeBytes("--"+boundary+"--\r\n");
				output.flush();
				output.close();
				if(!(new BufferedReader(new InputStreamReader(connection.getInputStream()))).readLine().equalsIgnoreCase("ok")){
					throw new Exception();
				}
				ThePhotoList.DeleteCurrentPhoto();
			}
			connection = HTTPConnect(new URL(url.getHost()+"/logout/main"),true,false);
			SetCookies(CookieList,connection);
			connection.getInputStream();
			connection.disconnect();
			connection = null;
		}catch(Exception e){
			System.out.println("Error: "+e.getLocalizedMessage());
			e.printStackTrace();
		}
}
	
	private String GetTag(){
		JComboBox TagChoice = new JComboBox(Tags);
		TagChoice.setEditable(true);
		int DiaRes = JOptionPane.showOptionDialog(	this,
													TagChoice,
													"Add Tag",
													JOptionPane.OK_CANCEL_OPTION,
													JOptionPane.PLAIN_MESSAGE,
													null, null, null);
		if(DiaRes == 0){
			return (String) TagChoice.getSelectedItem();
		}else{
			return null;
		}
	}
	
	public void valueChanged(ListSelectionEvent e) {
		// TODO Auto-generated method stub
		int Width;
	        if (ThePhotoList.getSelectedIndex() == -1) {
	        //No selection.
	        	Width = Body.getDividerLocation();
	        	Body.remove(PhotoDetails);
	        	PhotoDetails = new JPanel();
	        	Body.add(PhotoDetails);
	        	Body.setDividerLocation(Width);
	        } else {
	        //Selection.
	        	Width = Body.getDividerLocation();
	            Body.remove(PhotoDetails);
	            PhotoDetails = ThePhotoList.GetCurrentPhoto().GetDetails();
	        	Body.add(PhotoDetails);
	        	Body.setDividerLocation(Width);
	        }
	}
	
	
	class TagUpdateThread extends Thread {
		public void run(){
			UpdateTags(false);
		}
	}
	
	private void AddFiles(){
		if(fc.showOpenDialog(fc) == JFileChooser.APPROVE_OPTION){
			//RenderedOp image = JAI.create("FileLoad",fc.getSelectedFile().toString());
			ProgressMonitor progressmonitor = new ProgressMonitor(
								this,
								"Loading Selected Images",
								"Resizing/Moving Image 1",
								0,2*fc.getSelectedFiles().length);
			progressmonitor.setProgress(1);
			int count = 1;
			
			for (File SrcFile : fc.getSelectedFiles()){
				File DestFile = new File(
						WorkingDir.getString() +
						File.separator +
						SrcFile.getName());
				if(DestFile.exists()){
					int i = 0;
					while(DestFile.exists()){
						DestFile = new File(
								WorkingDir.getString() +
								File.separator +
								String.valueOf(i) +
								"_" +
								SrcFile.getName());
						i++;
					}	
				}
				// Currently Does not resize, just copies
				BufferedInputStream InStream = null;
				BufferedOutputStream OutStream = null;
				try {
					InStream = 
			            new BufferedInputStream(new FileInputStream(SrcFile));
			        OutStream = 
			            new BufferedOutputStream(new FileOutputStream(DestFile)); 
			        int Data;
			        while ((Data = InStream.read()) != -1){
			        	OutStream.write(Data);
			        }
				} catch (FileNotFoundException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} finally {
					try{
						if (InStream != null){
							InStream.close();
						}
						if (OutStream != null){
							OutStream.close();
						}
					} catch (IOException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}finally{}
				}
				progressmonitor.setProgress(count++);
				progressmonitor.setNote(String.format("Loading Image %d",(count)/2));
				
				try {
					Photo NewPhoto = new Photo(this,DestFile.toURI().toURL(),DestFile.getName(),PhotographerField.getText());
					ThePhotoList.Add(NewPhoto);
				} catch (MalformedURLException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				progressmonitor.setProgress(count++);
				progressmonitor.setNote(String.format("Resizing/Moving Image %d",(count+1)/2));
			}
		}
	}
	
	private void UpdateTags(Boolean Display){
		BufferedReader TagRead = null;
		try{
			URLConnection TagConn = (new URL("http://localhost:8888/yphoto/gettags")).openConnection();
			TagRead = new BufferedReader(
									new InputStreamReader(
											TagConn.getInputStream()));
			String Tag;
			String[] NewTags = new String[255];
			int index = 0;
			while((Tag=TagRead.readLine())!=null){
				NewTags[index++] = Tag;
				if(index == NewTags.length){
					String[] NewNewTags = new String[NewTags.length*2];
					System.arraycopy(NewTags, 0, NewNewTags, 0, index);
					NewTags = NewNewTags;
					NewNewTags = null;
				}
			}
			TagRead.close();
			TagRead = null;
			Tags = new String[index];
			System.arraycopy(NewTags, 0, Tags, 0, index);
			if(Display) JOptionPane.showMessageDialog(
					this,
					String.valueOf(index)+" Tags Retrieved.",
					"Tag List Downloaded",
					JOptionPane.INFORMATION_MESSAGE);
		} catch (Exception e) {
			if(Display) JOptionPane.showMessageDialog(
					this,
					"Could not connect and retreive the list of tags.",
					"Connection error.",
					JOptionPane.ERROR_MESSAGE);
		}finally{
			try {
				if(TagRead != null){TagRead.close();}
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
	
	public static void main(String[] args) {		
		new yphoto();
		// TODO Limit to one instance
	}

}
