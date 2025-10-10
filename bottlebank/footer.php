<script>
document.addEventListener('click', function(e){
  var menu = document.getElementById('sideMenu');
  var toggle = document.getElementById('menuToggle');
  if (!menu.contains(e.target) && e.target !== toggle) {
    menu.style.display = 'none';
  }
});

document.getElementById('menuToggle').addEventListener('click', function(){
  var menu = document.getElementById('sideMenu');
  menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
});
</script>
</body>
</html>
