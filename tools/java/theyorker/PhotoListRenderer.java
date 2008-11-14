package theyorker;

import java.awt.Component;
import javax.swing.BorderFactory;
import javax.swing.JLabel;
import javax.swing.JList;
import javax.swing.ListCellRenderer;

public class PhotoListRenderer extends JLabel implements ListCellRenderer {
	private static final long serialVersionUID = -1352747961321832144L;

	public Component getListCellRendererComponent(JList list, Object InObj,
			int index, boolean isSelected, boolean cellHasFocus) {
		// TODO Auto-generated method stub
		Photo ThePhoto = (Photo)InObj;
		if (isSelected) {
            setBackground(list.getSelectionBackground());
            setForeground(list.getSelectionForeground());
        } else {
            setBackground(list.getBackground());
            setForeground(list.getForeground());
        }
		setIcon(ThePhoto.getThumbnail());
		setText(ThePhoto.getTitle());
		setOpaque(true);
		setBorder(BorderFactory.createEmptyBorder(2,2,2,2));
		
		return this;		
	}
}
