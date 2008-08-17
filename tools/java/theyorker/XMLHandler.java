package theyorker;

import java.awt.event.ActionListener;
import java.io.File;
import java.util.Collection;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

class XMLHandler {
	Document Doc;
	File FName;
	
	public XMLHandler(File Fname){
		try{
			FName = Fname;
			DocumentBuilderFactory Factory =DocumentBuilderFactory.newInstance();
			DocumentBuilder Builder = Factory.newDocumentBuilder();
			if(!Fname.exists()){
				Doc = CreateFile(Builder);
			}else{
				Doc = Builder.parse(Fname);
			}
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	 
	private Document CreateFile(DocumentBuilder Builder) throws TransformerException{
		Document Doc = Builder.newDocument();
		Element root = Doc.createElement("yphoto");
		Doc.appendChild(root);
		
		Element TagSection = Doc.createElement("Tags");
		root.appendChild(TagSection);
		
		Element PhotoSection = Doc.createElement("Photos");
		root.appendChild(PhotoSection);
		
		Save();
		
		return Doc;
	}
	
	private void RemoveChildren(Node N){
		Node node = N.getFirstChild();
		Node newnode;
		while(node != null){
			newnode = node.getNextSibling();
			N.removeChild(node);
			node = newnode;}
	}

	public void UpdateTags(String[] Tags) throws Exception{
		if (Tags==null){return;}
		Node TagSection = Doc.getElementsByTagName("Tags").item(0);
		if (TagSection==null){
			throw new Exception("Unable to find Tags in XML DOM");
		}
		RemoveChildren(TagSection);
		Element em;
		for ( String Tag : Tags){ 
			em = Doc.createElement("Tag");
			em.appendChild(Doc.createTextNode(Tag));
			TagSection.appendChild(em);
		}
	}

	public String[] GetTags() throws Exception{
		NodeList TagList = Doc.getElementsByTagName("Tag");
		String[] Tags = new String[TagList.getLength()];
		for(int i=0;i<TagList.getLength();i++){
			Tags[i]=TagList.item(i).getNodeValue();
		}
		return Tags;
	}

	public Photo[] GetPhotos(ActionListener Listener) throws Exception{
		Node CurrPhotoNode = Doc.getElementsByTagName("Photos").item(0).getFirstChild();
		Collection<Photo> Photos;
		while(CurrPhotoNode != null){
			if (!CurrPhotoNode.getNodeName().equalsIgnoreCase("photo")){
				throw new Exception(
						"Invalid Structure: "+
						CurrPhotoNode.getNodeName()+
						"node found under Photos node. Expected a Photo node.");
			}
			Node CurrAttrNode = CurrAttrNode.getFirstChild();
			while(CurrAttrNode != null){
				final String ANName = CurrAttrNode.getNodeName();
				if(ANName.equals("")){
					
				}
			}
		}
		return (Photo[]) Photos.toArray();
	}	
	
	public void UpdatePhotos(PhotoList PL) throws Exception{
		Photo[] Photos = PL.getPhotoArray();
		if (Photos ==null){return;}
		Node PhotoSection = Doc.getElementsByTagName("Photos").item(0);
		if (PhotoSection==null){
			throw new Exception("Unable to find Photos in XML DOM");
		}
		RemoveChildren(PhotoSection);
		Element em,emTitle,emFName,emPhotographer,emTags,emTag;
		for (Photo APhoto : Photos){
			em = Doc.createElement("Photo");
			emTitle = Doc.createElement("Title");
			emTitle.appendChild(Doc.createTextNode(APhoto.GetTitle()));
			em.appendChild(emTitle);
			emFName = Doc.createElement("Filename");
			emFName.appendChild(Doc.createTextNode(APhoto.getFileName()));
			em.appendChild(emFName);
			emPhotographer = Doc.createElement("Photographer");
			emPhotographer.appendChild(Doc.createTextNode(APhoto.getPhotographer()));
			em.appendChild(emPhotographer);
			emTags = Doc.createElement("PTags");
			for(String Tag : APhoto.getTags()){
				emTag = Doc.createElement("PTag");
				emTag.appendChild(Doc.createTextNode(Tag));
				emTags.appendChild(emTag);
			}
			em.appendChild(emTags);
			PhotoSection.appendChild(em);
		}
	}
	
	public void Save() throws TransformerException{
		TransformerFactory transformerFactory = TransformerFactory.newInstance();
        Transformer transformer = transformerFactory.newTransformer();
        DOMSource source = new DOMSource(Doc);
        StreamResult result =  new StreamResult(FName);
        transformer.transform(source, result);		
	}
	
	public void UpdateAndSave(String[] Tags, PhotoList thePhotoList) throws Exception {
		UpdateTags(Tags);
		UpdatePhotos(thePhotoList);
		Save();
	}

}
