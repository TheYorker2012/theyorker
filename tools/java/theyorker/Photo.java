package theyorker;
import java.awt.event.ActionListener;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.Iterator;
import java.util.Set;
import javax.imageio.ImageIO;
import javax.swing.*;

/**
 * @author dg516
 *
 */
public class Photo {
	// STATIC DEFAULTS - private variables and their setters/getters
		static private Set<String> DefaultTags = null;
		static private String DefaultTitle = null;
		static private String DefaultPhotographer = null;
		
		/**
		 * @param NewTags Set of strings, each representing a tag.
		 */
		static public void setDefaultTags(Set<String> NewTags){
			DefaultTags = NewTags;
		}
		
		/**
		 * @param NewTitle
		 */
		static public void setDefaultTitle(String NewTitle){
			DefaultTitle = NewTitle;
		}
		
		/**
		 * @param NewPhotographer
		 */
		static public void setDefaultPhotographer(String NewPhotographer){
			DefaultPhotographer = NewPhotographer;
		}
		
		/**
		 * @return Set of Strings representing Tags.
		 */
		static public Set<String> getDefaultTags(){
			return DefaultTags;
		}
		
		/**
		 * @return DefaultTitle
		 */
		static public String getDefaultTitle(){
			return DefaultTitle;
		}
		
		/**
		 * @return DefaultPhotographer
		 */
		static public String getDefaultPhotographer(){
			return DefaultPhotographer;
		}
		
		// CONSTANTS
			// TODO PUT INTO RESOURCE BUNDLE (WITH STRINGS)
			private static int ThumbHeight = 48;
			private static int DetailsWidth = 480;

	// END OF STATIC
		
	private File PhotoFile = null;
	private String Title = DefaultTitle;
	private String Photographer = DefaultPhotographer;
	private ImageIcon Thumbnail= null;
	private Set<String> Tags = DefaultTags;
		
	/**
	 * Constructor taking minimum args. to create valid object.
	 * Only takes a URL to the photo to be represented.
	 * @param NewFile
	 * @throws IOException 
	 */
	public Photo(File NewFile) throws IOException {
		setFile(NewFile);
	}

// TODO NEEDED??	
//	public Photo(File NewFile, String NewTitle, String NewPhotographer) throws IOException{
//		setTitle(NewTitle);
//		setFile(NewFile);
//		setPhotographer(NewPhotographer);
//	}
	
	// Setters
		/**
		 * @param NewTitle
		 */
		public void setTitle(String NewTitle){
			this.Title = NewTitle;
			if(this.Thumbnail != null) this.Thumbnail.setDescription(NewTitle);
		}
	
		/**
		 * @param NewPhotographer
		 */
		public void setPhotographer(String NewPhotographer){
			this.Photographer = NewPhotographer;
		}
			
		/**
		 * sets file (and loads thumbnail)
		 * @param NewFile
		 * @throws IOException if file does not exist, cannot be read, or cannot
		 * 						create a thumbnail from it (i.e. not an image file)
		 */
		public void setFile(File NewFile) throws IOException{
			if (NewFile.isFile()){throw new IOException("Not a File");}
			if (!NewFile.canRead()){throw new IOException("Cannot Read File");}
			BufferedImage src;
			try {
				src = ImageIO.read(NewFile);
				BufferedImage dest = ImageLib.resizeBuffImage(
						src,
						ThumbHeight*src.getWidth()/src.getHeight(),
						ThumbHeight);
				this.Thumbnail = new ImageIcon(dest);
				this.Thumbnail.setDescription(getTitle());
				this.PhotoFile=NewFile;
			} catch (IOException e) {
				this.PhotoFile=null;
				this.Thumbnail = null;
				throw e;
			}			
		}
		
		/**
		 * @param tags the tags to set
		 */
		public void setTags(Set<String> tags) {
			this.Tags = tags;
		}
	
	// Getters
		/**
		 * @return title
		 */
		public String getTitle(){
			return this.Title;
		}
		
		/**
		 * @return photographer
		 */
		public String getPhotographer(){
			return this.Photographer;
		}
	
		/**
		 * @return File
		 */
		public File getFile(){
			return this.PhotoFile;
		}
		
		/**
		 * @return thumbnail ImageIcon
		 */
		public ImageIcon getThumbnail(){
				return this.Thumbnail;
		}
		
		/**
		 * @param listener listener for changes to the PhotoPanel
		 * @return PhotoPanel for this Photo
		 */
		public JComponent getDetails(ActionListener listener){
			return new PhotoPanel(	getPhotographer(),
									getTitle(),
									getTags(),
									getFile(),
									DetailsWidth,
									this,
									listener);
		}
	
		/**
		 * @return set of tags
		 */
		public Set<String> getTags(){
			return this.Tags;
		}
	
	// Tag Handling	
		/**
		 * @param Tag tag to delete
		 */
		public void deleteTag(String Tag){
			this.Tags.remove(Tag);
		}
	
		/**
		 * @param newTag Tag to add
		 */
		public void addTag(String newTag){
			this.Tags.add(newTag);
		}
		
		/**
		 * @return Space separated string of tag names
		 */
		public String getTagString(){
			Iterator<String> it = this.Tags.iterator();
			String TagString = "";
			
			while(it.hasNext()){
				TagString.concat(it.next() + (it.hasNext()?" ":""));
			}
			return TagString;
		}
		
	/**
	 * @throws IOException If file cannot be deleted
	 */
	public void deletePhotoFile() throws IOException{
		if (!this.PhotoFile.delete())
			throw new IOException("Unable to delete PhotoFile");
	}

//	public BufferedInputStream getFile() throws FileNotFoundException, URISyntaxException{
		/**
		 * TODO REDO BASED ON NEW UPLOAD CODE.
		 */
//		return null;
//		return new BufferedInputStream(new FileInputStream(new File(PhotoURL.toURI())));
//	}

}
