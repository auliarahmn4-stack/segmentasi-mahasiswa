document.addEventListener('DOMContentLoaded',()=>{
 const menu=document.getElementById('menuToggle'),sidebar=document.getElementById('sidebar');
 if(menu&&sidebar)menu.addEventListener('click',()=>sidebar.classList.toggle('open'));
 document.querySelectorAll('[data-dismiss]').forEach(b=>b.addEventListener('click',()=>b.closest('.alert')?.remove()));
 document.querySelectorAll('[data-toggle-password]').forEach(b=>b.addEventListener('click',()=>{const input=document.getElementById(b.dataset.togglePassword);if(!input)return;input.type=input.type==='password'?'text':'password';b.textContent=input.type==='password'?'Lihat':'Sembunyikan';}));
 document.querySelectorAll('[data-confirm-form]').forEach(b=>b.addEventListener('click',()=>{if(confirm('Hapus data ini? Tindakan ini tidak dapat dibatalkan.'))document.getElementById(b.dataset.confirmForm)?.requestSubmit();}));
 document.querySelectorAll('[data-loading-form]').forEach(f=>f.addEventListener('submit',()=>{const b=f.querySelector('[data-loading-text]');if(b){b.disabled=true;b.textContent=b.dataset.loadingText;}}));
 if(typeof Chart!=='undefined'){
   const cluster=document.getElementById('clusterData');if(cluster){const d=JSON.parse(cluster.textContent);new Chart(document.getElementById('clusterChart'),{type:'doughnut',data:{labels:d.labels,datasets:[{data:d.values,backgroundColor:['#2d67d8','#7d9fdb','#39a889','#e4b15e','#8b7ed6','#de8291'],borderWidth:0,hoverOffset:5}]},options:{maintainAspectRatio:false,cutout:'66%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,boxWidth:7,padding:16,font:{family:'DM Sans',size:10}}}}}});}
   const ed=document.getElementById('elbowData');if(ed){const d=JSON.parse(ed.textContent);new Chart(document.getElementById('elbowChart'),{type:'line',data:{labels:d.labels.map(k=>'K = '+k),datasets:[{label:'SSE',data:d.values,borderColor:'#2d67d8',backgroundColor:'#2d67d820',fill:true,tension:.25,pointRadius:4}]},options:{maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#edf0f5'}},x:{grid:{display:false}}}}});}
   const sd=document.getElementById('silhouetteData');if(sd){const d=JSON.parse(sd.textContent);new Chart(document.getElementById('silhouetteChart'),{type:'bar',data:{labels:d.labels.map(k=>'K = '+k),datasets:[{label:'Silhouette',data:d.values,backgroundColor:'#7d9fdb',borderRadius:5}]},options:{maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{min:-1,max:1,grid:{color:'#edf0f5'}},x:{grid:{display:false}}}}});}
 }
});
