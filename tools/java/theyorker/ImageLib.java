/**
 * 
 */
package theyorker;

import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.RenderingHints;
import java.awt.image.BufferedImage;

/**
 * @author dg516
 *
 */
public final class ImageLib {

	/**
	 * Resize a buffered Image
	 * @param src
	 * @param Width
	 * @param Height
	 * @return resized buffered image
	 */
	static public BufferedImage resizeBuffImage(Image src, int Width, int Height){
		BufferedImage dest = new BufferedImage(Width,Height,BufferedImage.TYPE_INT_RGB);
		Graphics2D g2 = dest.createGraphics();
		g2.setRenderingHint(RenderingHints.KEY_INTERPOLATION, RenderingHints.VALUE_INTERPOLATION_BILINEAR);
		g2.drawImage(src, 0, 0, Width, Height, null);
		g2.dispose();
		return dest;
	}
	
}
