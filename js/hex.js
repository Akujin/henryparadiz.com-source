var 部 = {
  onload: function() {
    //$('a').click(this.clickOverride.bind(this));
  },
  clickOverride: function(e) {
    // load internal clicks via this handler
    if( location.hostname === e.toElement.hostname ) {
      e.preventDefault();
      this.loadLocal.bind(this,e.toElement.getAttribute('href'))();
    }
  },
  loadLocal: function(href) {
    console.log(href)
  }

}

$('document').ready(部.onload.bind(部));