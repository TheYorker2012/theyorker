/*------------------------------------------------------------------------------------
	Preloader
	A very simple image preloader object
 	Usage:
    Preloader.add(path);
    Preloader.onFinish(func);
    Preloader.load();
      path: 		A string or array of strings of image paths to preload
      func:     A function or array of functions to be called after images are loaded
      load():   Start the preloader
------------------------------------------------------------------------------------*/

var Slideshow = {
  callbacks: [],
  images: [],
  loadedImages: [],
  imagesLoaded: 0,
  showImages: [],
  current: 0,
  timer: 0,
  
  add: function(image){
    if (typeof image == 'string') this.images.push(image);
    if (typeof image == 'array' || typeof image == 'object'){
      for (var i=0; i< image.length; i++){
        this.images.push(image[i]);
      }
    }
  },
  onFinish: function(func){
    if (typeof func == 'function') this.callbacks.push(func);
    if (typeof func == 'array' || typeof func == 'object'){
      for (var i=0; i< func.length; i++){
        this.callbacks.push(func[i]);
      }
    }
  },
  load: function(){
    for(var i=0; i<this.images.length; i++){
      this.loadedImages[i] = new Image();
      eval("this.loadedImages[i].onload = this.checkFinished('" + this.images[i] + "');");
      this.loadedImages[i].src = this.images[i];
    }
    setTimeout('Slideshow.nextImage()',1000);
  },
  
  checkFinished: function(imgpath){
    Slideshow.showImages[Slideshow.imagesLoaded] = imgpath;
    Slideshow.imagesLoaded++;
    /*if (this.imagesLoaded == this.images.length) this.fireFinish();*/
  },

  fireFinish: function(){
    for (var i=0; i<this.callbacks.length; i++){
      this.callbacks[i]();
    }
    this.images = [];
    this.loadedImages = [];
    this.imagesLoaded = 0;
    this.callbacks = [];
  },

  nextImage: function(){
    if (this.imagesLoaded > 0) {
      document.getElementById('ss').style.background = "url('" + this.showImages[this.current] + "')";
      Effect.Shrink('changeme', {queue: 'end'});
    }
    timer=setTimeout('Slideshow.resetImage()',3000);
  },

  resetImage: function(){
    if (this.imagesLoaded > 0) {
      Effect.Appear('changeme', {queue: 'end'});
      document.getElementById('changeme').src = this.showImages[this.current];
      this.current = (this.current + 1) % this.imagesLoaded
    }
    timer=setTimeout('Slideshow.nextImage()',2000);
  },

  reset: function(){
    this.callbacks = [];
    this.images = [];
    this.loadedImages = [];
    this.imagesLoaded = 0;
    this.showImages = [];
    this.current = 0;
    clearTimeout(timer);
  }

}