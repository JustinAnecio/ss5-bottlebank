</div>

<script>
const toggle = document.getElementById('menuToggle');
const menu = document.getElementById('sideMenu');

toggle.addEventListener('click', function() {
  menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
});

document.addEventListener('click', function(e) {
  if (!menu.contains(e.target) && e.target !== toggle) {
    menu.style.display = 'none';
  }
});
</script>

</body>
</html>
