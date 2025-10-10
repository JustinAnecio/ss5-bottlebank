var links = document.querySelectorAll('.menu-link');
var main = document.getElementById('mainContent');

links.forEach(function(link){
  link.addEventListener('click', function(e){
    e.preventDefault();
    var url = this.getAttribute('href');
    main.innerHTML = "Loading...";

    fetch(url)
      .then(function(resp){ return resp.text(); })
      .then(function(html){
        main.innerHTML = html;
        document.getElementById('sideMenu').style.display = 'none';
      })
      .catch(function(){ main.innerHTML = "Failed to load."; });
  });
});