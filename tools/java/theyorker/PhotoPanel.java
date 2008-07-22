package theyorker;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.event.ActionListener;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.Set;
import javax.imageio.ImageIO;
import javax.swing.DefaultListModel;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JList;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTextField;
import javax.swing.ListSelectionModel;

/**
 * @author dg516
 *
 */
public class PhotoPanel extends JScrollPane {
	private static final long serialVersionUID = 5857964777761267311L;
	private JTextField PhotographerField;
	private JTextField TitleField;
	private JList TagListBox;
	private DefaultListModel TagListModel;
	private Photo thePhoto;
	
	/**
	 * @param Title
	 * @param Photographer
	 * @param Tags
	 * @param PhotoFile
	 * @param DetailsWidth
	 * @param theNPhoto
	 * @param listener
	 */
	public PhotoPanel(	String Title,
						String Photographer,
						Set<String> Tags,
						File PhotoFile,
						int DetailsWidth,
						Photo theNPhoto,
						ActionListener listener) {
		super();	// extends ScrollPane - do normal constructor
		this.thePhoto = theNPhoto;
		C con = new C();
		
		JPanel ThePanel = new JPanel();
		ThePanel.setLayout(new GridBagLayout());
		BufferedImage src;
		try {
			src = ImageIO.read(PhotoFile);
			BufferedImage dest = ImageLib.resizeBuffImage(
					src,
					DetailsWidth,
					DetailsWidth*src.getHeight()/src.getWidth());
			JLabel PhotoLabel = new JLabel(new ImageIcon(dest));
			PhotoLabel.setAlignmentX(Component.CENTER_ALIGNMENT);			
			ThePanel.add(
					PhotoLabel,
					con.getCon(1, 1, C.RELATIVE, 1, C.UNDEF, C.UNDEF));
			
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		con.anchor = C.LINE_START;
		ThePanel.add(
				new JLabel("Title:"),
				con.getCon(1, 2, 1, 1, 1, C.UNDEF));
		this.TitleField = new JTextField(20);
		this.TitleField.setText(Title);
		this.TitleField.setMaximumSize(new Dimension(300,30));
		ThePanel.add(
				this.TitleField,
				con.getCon(2, 2, 2, 1, 0, C.UNDEF));
		ThePanel.add(
				new JLabel("Photographer:"),
				con.getCon(1, 3, 1, 1, C.UNDEF, C.UNDEF));
		this.PhotographerField = new JTextField(20);
		this.PhotographerField.setText(Photographer);
		ThePanel.add(
				this.PhotographerField,
				con.getCon(2, 3, 2, 1, C.UNDEF, C.UNDEF));
		ThePanel.add(
				new JLabel("Tags:"),
				con.getCon(1, 4, 1, 1, C.UNDEF, C.UNDEF));
		con.fill = GridBagConstraints.HORIZONTAL;
		this.TagListModel = new DefaultListModel();
		for(String Tag : Tags){
			this.TagListModel.addElement(Tag);
		}
		this.TagListBox = new JList();
		this.TagListBox.setModel(this.TagListModel);
		this.TagListBox.setSelectedIndex(ListSelectionModel.SINGLE_SELECTION);
		this.TagListBox.setLayoutOrientation(JList.VERTICAL);
		this.TagListBox.setVisibleRowCount(4);
		Dimension dim = new Dimension();
		dim.width = 150;
		this.TagListBox.setMinimumSize(dim);
		ThePanel.add(
				new JScrollPane(this.TagListBox),
				con.getCon(2,4,1,2,0.2,C.UNDEF));
		
		JButton TagAdd = new JButton("Add");
		TagAdd.setActionCommand("Photo Add Tag");
		TagAdd.addActionListener(listener);

		con.fill = GridBagConstraints.NONE;
		ThePanel.add(
				TagAdd,
				con.getCon(3, 4, 1, 1, 0, C.UNDEF));
		JButton TagDel = new JButton("Delete");
		TagDel.setActionCommand("Photo Delete Tag");
		TagDel.addActionListener(listener);
		ThePanel.add(
				TagDel,
				con.getCon(3, 5, 1, 1, C.UNDEF, C.UNDEF));			
		ThePanel.setAlignmentX(Component.CENTER_ALIGNMENT);
		ThePanel.setMinimumSize(new Dimension(DetailsWidth,100));
		ThePanel.setMaximumSize(new Dimension(DetailsWidth, 1000));
		ThePanel.add(
				new JLabel(),
				con.getCon(1, 1, 0, 0, C.UNDEF, C.UNDEF));
		con.gridy = 6;
		ThePanel.add(new JLabel(),con);
		con.gridx = 4;
		ThePanel.add(new JLabel(),con);
		con.gridy = 0;
		ThePanel.add(new JLabel(),con);	
		getViewport().add(ThePanel);
		setMaximumSize(new Dimension(DetailsWidth,1000));
	}

	private class C extends GridBagConstraints{
		private static final long serialVersionUID = 4527016997800387220L;
		static final int UNDEF = -1;
		static final int RELATIVE = GridBagConstraints.RELATIVE;
		static final int BOTH = GridBagConstraints.BOTH;
		static final int CENTER = GridBagConstraints.CENTER;
		static final int EAST = GridBagConstraints.EAST;
		static final int FIRST_LINE_END = GridBagConstraints.FIRST_LINE_END;
		static final int FIRST_LINE_START = GridBagConstraints.FIRST_LINE_START;
		static final int HORIZONTAL = GridBagConstraints.HORIZONTAL;
		static final int LAST_LINE_END = GridBagConstraints.LAST_LINE_END;
		static final int LAST_LINE_START = GridBagConstraints.LAST_LINE_START;
		static final int LINE_END = GridBagConstraints.LINE_END;
		static final int LINE_START = GridBagConstraints.LINE_START;
		static final int NONE = GridBagConstraints.NONE;
		static final int NORTH = GridBagConstraints.NORTH;
		static final int NORTHEAST = GridBagConstraints.NORTHEAST;
		static final int NORTHWEST = GridBagConstraints.NORTHWEST;
		static final int PAGE_END = GridBagConstraints.PAGE_END;
		static final int PAGE_START = GridBagConstraints.PAGE_START;
		static final int REMAINDER = GridBagConstraints.REMAINDER;
		static final int SOUTH = GridBagConstraints.SOUTH;
		static final int SOUTHEAST = GridBagConstraints.SOUTHEAST;
		static final int SOUTHWEST = GridBagConstraints.SOUTHWEST;
		static final int VERTICAL = GridBagConstraints.VERTICAL;
		static final int WEST = GridBagConstraints.WEST;
		
		/**
		 * Default Constructor (explicit)
		 */
		public C(){
			super();
		}
		
		/**
		 * @param x Constraint gridx
		 * @param y Constraint gridy
		 * @param w Constraint gridwidth
		 * @param h Constraint gridheight
		 * @param wx Constraint weightx
		 * @param wy Constraint weighty
		 * @return Constraint
		 */
		public GridBagConstraints getCon(int x,int y,int w, int h, double wx, double wy){
			if(x>=0) this.gridx = x;
			if(y>=0) this.gridy = y;
			if(w>=0) this.gridwidth = w;
			if(h>=0) this.gridheight = h;
			if(wx>=0) this.weightx = wx;
			if(wy>=0) this.weighty = wy;
			return this;
		}		
	}
	
	
	/**
	 * 
	 */
	public void deleteCurrentTag(){
		if(this.TagListBox.getSelectedIndex()>=0){
			String Tag = (String) this.TagListBox.getSelectedValue();
			this.thePhoto.deleteTag(Tag);
			this.TagListModel.remove(this.TagListBox.getSelectedIndex());
		}
	}
	
	/**
	 * @param NewTag
	 */
	public void addTag(String NewTag){
		this.TagListModel.addElement(NewTag);
		this.thePhoto.addTag(NewTag);
	}
}
