(function(){
  const menuBtn=document.getElementById('menuBtn');
  const menu=document.getElementById('menu');
  menuBtn&&menuBtn.addEventListener('click',()=>{
    const open=menu.classList.toggle('open');
    menuBtn.setAttribute('aria-expanded',open?'true':'false');
  });
  const year=document.getElementById('year'); if(year) year.textContent=new Date().getFullYear();
  const path=location.pathname.replace(/\/index\.html$/,'');
  document.querySelectorAll('nav a[data-nav]').forEach(a=>{
    const href=a.getAttribute('href').replace(/\/index\.html$/,'');
    if(href===path || (href==='/'&& (path==='/'||path===''))) a.classList.add('active');
  });
})();