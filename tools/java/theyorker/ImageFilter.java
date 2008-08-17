package theyorker;

import javax.swing.filechooser.*;

/**
 * @author dg516
 *
 */
public class ImageFilter extends FileFilter {

	/**
	 * @see javax.swing.filechooser.FileFilter#accept(java.io.File)
	 */
	@Override
	public boolean accept(java.io.File f){
		if(f.isDirectory()) return true;

		String extension = f.getName();
        extension = extension.substring(extension.lastIndexOf('.')+1);
		return (	extension.equalsIgnoreCase("jpeg")||
					extension.equalsIgnoreCase("jpg"));
	}

	/**
	 * @see javax.swing.filechooser.FileFilter#getDescription()
	 */
	@Override
	public String getDescription() {
		return "jpeg files (*.jpeg,*.jpg)";
	}
}
