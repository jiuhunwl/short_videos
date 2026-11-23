(() => {
  if(!window.ShiyingAndroid)return;
  const $=id=>document.getElementById(id),make=(tag,cls,text)=>{const el=document.createElement(tag);if(cls)el.className=cls;if(text)el.textContent=text;return el;};
  const icon=paths=>{const svg=document.createElementNS('http://www.w3.org/2000/svg','svg');svg.setAttribute('viewBox','0 0 24 24');svg.setAttribute('aria-hidden','true');for(const d of paths){const path=document.createElementNS(svg.namespaceURI,'path');path.setAttribute('d',d);svg.append(path);}return svg;};
  const icons={parse:['M8 3H3v5M16 3h5v5M21 16v5h-5M8 21H3v-5','m10 8 6 4-6 4Z'],materials:['M4 5h16v14H4z','M4 9h16M8 5v4M16 5v4','m10 12 4 2-4 2Z'],settings:['M4 6h16M4 12h16M4 18h16','M8 3v6M16 9v6M10 15v6']};
  document.body.classList.add('android-app');
  const main=document.querySelector('main'),oldHeader=main.querySelector('header'),hero=main.querySelector('.hero');
  const server=window.ShiyingAndroid.serverUrl?.()||'device';
  const top=make('header','app-topbar'),brand=make('div','app-brand');brand.append(icon(icons.parse),make('span','','拾影'));
  const pageTitle=make('h1','app-page-title','链接解析'),mode=make('span','app-mode',server==='device'?'手机本地':server.includes('workers.dev')?'云端':'局域网');
  top.append(brand,mode);main.prepend(top);
  const heading=make('div','app-heading');heading.append(pageTitle);main.insertBefore(heading,oldHeader);
  const pages={};
  icons.history=['M4 5h16v15H4z','M8 2v6M16 2v6M4 10h16','M8 14h8M8 17h5'];
  for(const name of ['parse','materials','history','settings']){pages[name]=make('section','app-page');pages[name].dataset.page=name;pages[name].setAttribute('aria-label',{parse:'链接解析',materials:'素材',history:'下载历史',settings:'设置'}[name]);main.append(pages[name]);}
  const historySlot=make('div');historySlot.id='app-history';pages.history.append(historySlot);
  const headline=hero.querySelector('h1');headline.textContent='保存你喜欢的作品';hero.querySelector('p').textContent='抖音 · 快手 · 小红书';
  hero.querySelector('.eyebrow').hidden=true;hero.querySelector('.hero-art').hidden=true;
  pages.parse.append(hero,$('parser'));
  $('parser').querySelector('h2').textContent='添加作品';$('parser').querySelector('label[for="share"]').textContent='分享链接';$('parse-button').textContent='开始解析';
  $('share').placeholder='粘贴链接，或整段分享文案';
  $('share').value='';
  $('result').querySelector('p.field-note').textContent='可逐个保存或打包 ZIP。文件大小仅受手机剩余空间和平台可用性影响。';
  const empty=make('div','app-empty');empty.id='app-materials-empty';empty.append(icon(icons.materials),make('h2','','素材会出现在这里'),make('p','','解析一个作品，再选择要保存的内容。'));
  const go=make('button','primary','去解析作品');go.type='button';go.onclick=()=>{show('parse');$('share').focus();};empty.append(go);pages.materials.append(empty,$('result'));
  for(const id of ['app-folder-settings','app-history-settings','app-startup-settings','app-service-settings']){const slot=make('div');slot.id=id;pages.settings.append(slot);}
  const account=make('section','settings-group app-account');account.append(make('h2','','账号'),$('account'));account.hidden=server==='device';pages.settings.append(account);
  const about=make('div','app-about');about.append(make('strong','','拾影 1.1.3'),make('p','','请尊重原创，仅保存已获授权的内容。'));pages.settings.append(about);
  oldHeader.hidden=true;document.querySelector('aside').hidden=true;$('platforms').hidden=true;main.querySelector('.bottom-note').hidden=true;
  main.insertBefore($('status'),pages.parse);
  const dock=make('nav','app-dock');dock.setAttribute('aria-label','主导航');const buttons={};let current='parse';
  for(const [name,label] of [['parse','解析'],['materials','素材'],['history','历史'],['settings','设置']]){
    const button=make('button','app-tab');button.type='button';button.dataset.page=name;button.setAttribute('aria-controls','app-page-'+name);pages[name].id='app-page-'+name;button.append(icon(icons[name]),make('span','',label));button.onclick=()=>show(name);buttons[name]=button;dock.append(button);
  }
  document.body.append(dock);
  function show(name){
    if(!pages[name])return;current=name;
    for(const key of Object.keys(pages)){pages[key].hidden=key!==name;buttons[key].classList.toggle('active',key===name);if(key===name)buttons[key].setAttribute('aria-current','page');else buttons[key].removeAttribute('aria-current');}
    pageTitle.textContent={parse:'链接解析',materials:'作品素材',history:'下载历史',settings:'设置'}[name];empty.hidden=!$('result').hidden;
    if(name==='history')document.dispatchEvent(new Event('shiying:history'));
    window.scrollTo({top:0,behavior:'instant'});
    window.requestAnimationFrame?.(()=>window.scrollTo({top:0,behavior:'instant'}));
  }
  window.ShiyingApp={show,back(){if(current==='parse')return false;show('parse');return true;}};
  document.addEventListener('focusin',event=>{if(event.target.matches('input,textarea'))document.body.classList.add('keyboard-open');});
  document.addEventListener('focusout',()=>setTimeout(()=>{if(!document.activeElement?.matches('input,textarea'))document.body.classList.remove('keyboard-open');},0));
  show('parse');
})();
