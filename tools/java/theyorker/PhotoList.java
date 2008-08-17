package theyorker;

import java.io.IOException;
import java.util.Enumeration;

import javax.swing.DefaultListModel;
import javax.swing.JList;

public class PhotoList extends JList {
	private static final long serialVersionUID = 7976503197862901662L;
	private DefaultListModel ListModel;
	
	public PhotoList(){
		ListModel= new DefaultListModel();
		setModel(ListModel);
	}
	
	public void Add(Photo NewPhoto){
		int index=ListModel.size();
		//AllPhotos.add(new PhotoIndexRec(NewPhoto,index));

		ListModel.insertElementAt(NewPhoto,index);
		setSelectedIndex(index);
		ensureIndexIsVisible(index);
	}
	
	public Photo GetPhoto(int Index){
		return (Photo) ListModel.get(Index);
	}
	
	public void RemovePhoto(int Index) throws IOException{
		((Photo)ListModel.get(Index)).deletePhotoFile();
		ListModel.remove(Index);
	}
	
	
	public Photo GetCurrentPhoto(){
		return GetPhoto(getSelectedIndex());
	}
	
	public void DeleteCurrentPhoto() throws IOException{
		RemovePhoto(getSelectedIndex());
	}

	public boolean isEmpty() {
		return ListModel.isEmpty();
	}
	
	public Photo[] getPhotoArray(){
		if (ListModel.size() == 0) {return null;}
		Enumeration<?> PhotoEnum =  ListModel.elements();
		Photo Photos[] = new Photo[ListModel.size()];
		int i = Photos.length-1;
		while(i>=0 && PhotoEnum.hasMoreElements()){
			Photos[i] = (Photo) PhotoEnum.nextElement();
		}
		return Photos;
	}

}
