// Read-only browser checks. Artifacts live outside the theme in the OS temp folder.
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawn } = require('node:child_process');
const out = path.join(os.tmpdir(), 'vectone-cards-sidebar');
fs.mkdirSync(out, { recursive: true });
const mode = process.argv[2] || 'before';
const delay = ms => new Promise(r => setTimeout(r, ms));
(async () => {
  const proc = spawn('C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe', ['--headless=new', '--disable-gpu', '--remote-debugging-port=9337', '--no-first-run', '--user-data-dir=' + path.join(out, 'profile'), 'about:blank'], { windowsHide: true, stdio: 'ignore' });
  let ws;
  try {
    let tabs;
    for (let i = 0; i < 40; i++) { try { tabs = await (await fetch('http://127.0.0.1:9337/json')).json(); break; } catch { await delay(250); } }
    ws = new WebSocket(tabs.find(t => t.type === 'page').webSocketDebuggerUrl);
    await new Promise(r => ws.addEventListener('open', r, { once: true }));
    let id = 0; const pending = new Map(); const errors = [];
    ws.addEventListener('message', e => { const m = JSON.parse(e.data); if (m.id) { const p = pending.get(m.id); pending.delete(m.id); m.error ? p.reject(m.error) : p.resolve(m.result); } else if (m.method === 'Runtime.exceptionThrown') errors.push(m.params.exceptionDetails); });
    const send = (method, params = {}) => new Promise((resolve, reject) => { const n = ++id; pending.set(n, { resolve, reject }); ws.send(JSON.stringify({ id: n, method, params })); });
    const evaluate = async expression => { const r = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true }); if (r.exceptionDetails) throw r.exceptionDetails; return r.result.value; };
    await send('Page.enable'); await send('Runtime.enable');
    if (mode === 'baseline-filters') {
      const checkpoint = path.resolve('partdo-child-checkpoint-before-cards-sidebar-20261007-092546/assets/vectone-v3');
      ws.addEventListener('message', e => {
        const m=JSON.parse(e.data); if(m.method!=='Fetch.requestPaused')return;
        const js=m.params.request.url.includes('/js/app.js');
        send('Fetch.fulfillRequest',{requestId:m.params.requestId,responseCode:200,responseHeaders:[{name:'Content-Type',value:js?'application/javascript':'text/css'}],body:fs.readFileSync(path.join(checkpoint,js?'js/app.js':'css/home.css')).toString('base64')});
      });
      await send('Fetch.enable',{patterns:[{urlPattern:'*/assets/vectone-v3/js/app.js*'},{urlPattern:'*/assets/vectone-v3/css/home.css*'}]});
    }
    const report = { errors, widths: [] };
    if (['filters','baseline-filters','mobile'].includes(mode)) {
      await send('Emulation.setDeviceMetricsOverride', { width:1440,height:1000,deviceScaleFactor:1,mobile:false });
      const home = async () => {
        await Promise.race([send('Page.navigate',{url:'http://127.0.0.1:8085/'}),delay(30000)]);
        for(let i=0;i<180;i++){if(await evaluate("document.readyState==='complete' && !!document.querySelector('.vectone-compare-bar')"))return;await delay(500);}
        throw Error('Homepage initialization timeout');
      };
      const waitQuery = async key => {
        for(let i=0;i<180;i++){if(await evaluate(`document.readyState==='complete' && new URL(location.href).searchParams.has(${JSON.stringify(key)})`))return;await delay(500);}
        throw Error('Filter navigation timeout: '+key);
      };
      await home();
      if (mode === 'mobile') {
        await send('Emulation.setDeviceMetricsOverride',{width:390,height:900,deviceScaleFactor:1,mobile:false});
        const toggle=await evaluate(`(() => {document.querySelector('.cmplz-close')?.click();const e=[...document.querySelectorAll('a,button')].find(e=>/^filter$|^filtre$/i.test(e.textContent.trim())&&e.getBoundingClientRect().width>0);if(!e)return null;e.click();return {class:e.className,text:e.textContent};})()`);
        await delay(500);
        const panel=await evaluate(`(() => {const e=document.querySelector('.filtered-sidebar .sidebar-inner'),r=e.getBoundingClientRect();return {x:r.x,width:r.width,overflow:e.scrollWidth>e.clientWidth,viewport:innerWidth};})()`);
        const shot=await send('Page.captureScreenshot',{format:'png'});fs.writeFileSync(path.join(out,'mobile-sidebar.png'),Buffer.from(shot.data,'base64'));
        console.log(JSON.stringify({toggle,panel,errors}));await send('Browser.close');return;
      }
      const original = await evaluate(`(() => {const f=[...document.querySelectorAll('.filtered-sidebar .service-search-form')].find(f=>f.getBoundingClientRect().width>0); const s=f.querySelector('select[name="filter_5g-disponible"]');s.value='oui';s.dispatchEvent(new Event('change',{bubbles:true}));const fields=[...new FormData(f).entries()]; f.requestSubmit(f.querySelector('button')); return fields;})()`);
      await waitQuery('filter_5g-disponible');
      report.fiveG=await evaluate(`({url:location.href,products:document.querySelectorAll('.products .product-wrapper').length})`);
      report.fiveG.submitted=original;
      await home();
      await evaluate("document.querySelector('.widget_price_filter .ui-slider-handle').focus()");
      await send('Input.dispatchKeyEvent',{type:'keyDown',key:'ArrowRight',code:'ArrowRight',windowsVirtualKeyCode:39});
      await send('Input.dispatchKeyEvent',{type:'keyUp',key:'ArrowRight',code:'ArrowRight',windowsVirtualKeyCode:39});
      report.priceInput=await evaluate(`(() => {const f=document.querySelector('.widget_price_filter form');const data=[...new FormData(f).entries()];f.requestSubmit(f.querySelector('button'));return data;})()`);
      await waitQuery('min_price');
      report.price=await evaluate(`({url:location.href,prices:[...document.querySelectorAll('.products .product-wrapper .price')].map(p=>p.textContent.trim())})`);
      fs.writeFileSync(path.join(out,mode+'.json'),JSON.stringify(report,null,2));
      console.log(JSON.stringify(report,null,2)); await send('Browser.close');return;
    }
    for (const width of (process.argv[3] ? [Number(process.argv[3])] : [1440, 1024, 768, 390])) {
      await send('Emulation.setDeviceMetricsOverride', { width, height: 1000, deviceScaleFactor: 1, mobile: false });
      await Promise.race([send('Page.navigate', { url: 'http://127.0.0.1:8085/' }), delay(30000)]);
      for (let i = 0; i < 180; i++) { if (await evaluate("document.readyState === 'complete' && !!document.querySelector('.vectone-compare-bar')")) break; await delay(500); }
      if (!await evaluate("!!document.querySelector('.vectone-compare-bar')")) throw new Error('Page did not finish initializing: '+width);
      await delay(800);
      const state = await evaluate(`(() => {
        const q = s => [...document.querySelectorAll(s)];
        const cards = q('.products .product-wrapper');
        const attrs = el => Object.fromEntries([...el.attributes].map(a => [a.name,a.value]));
        const sections = ['.products .product-wrapper','.filtered-sidebar .sidebar-inner'];
        return {width:innerWidth,scrollWidth:document.documentElement.scrollWidth,products:cards.length,
          links:cards.map(c=>[...c.querySelectorAll('a[href]')].map(attrs)),
          featuredLinks:q('.vm-v3-offer__cta').map(a=>({href:a.getAttribute('href'),target:a.getAttribute('target'),rel:a.getAttribute('rel')})),
          awin:q('a[href*="awin1.com"]').filter(a=>!a.closest('.vm-v3-featured,.vectone-compare-modal')).map(a=>a.getAttribute('href')),
          forms:q('form').map(f=>({action:f.getAttribute('action'),method:f.getAttribute('method'),fields:[...f.querySelectorAll('[name]')].map(attrs)})),
          featured:cards.filter(c=>c.closest('.featured')).map(c=>c.textContent.trim().slice(0,150)),
          sections:sections.map(s=>{const e=document.querySelector(s);const b=e?.getBoundingClientRect();return {selector:s,x:b?.x,width:b?.width,height:b?.height,overflow:e?e.scrollWidth>e.clientWidth:false};}),
          controls:q('.filtered-sidebar input,.filtered-sidebar select,.filtered-sidebar button').map(attrs),
          cardHeights:cards.map(c=>c.getBoundingClientRect().height), compareButtons:q('.vectone-compare-button').length};
      })()`);
      report.widths.push(state);
      if (mode === 'inspect') console.log(await evaluate(`(() => { const c=document.querySelector('.products .product-wrapper');return {html:c.querySelector('.product-footer').outerHTML,styles:[...c.querySelectorAll('.product-footer,.product-footer-inner,.price,.vectone-compare-button,.product-footer a')].map(e=>{const s=getComputedStyle(e);return {class:e.className,display:s.display,position:s.position,top:s.top,left:s.left,right:s.right,gridArea:s.gridArea,width:s.width,transform:s.transform}})};})()`));
      await evaluate("document.querySelector('.cmplz-close')?.click(); window.scrollTo({top:document.querySelector('.shop-container').getBoundingClientRect().top+window.scrollY-16,behavior:'instant'})");
      await delay(500);
      const shot = await send('Page.captureScreenshot', { format: 'png' });
      fs.writeFileSync(path.join(out, mode + '-' + width + '.png'), Buffer.from(shot.data, 'base64'));
      await evaluate("window.scrollTo({top:document.querySelector('.products .product-wrapper').getBoundingClientRect().top+window.scrollY-140,behavior:'instant'})");
      await delay(300);
      const offersShot = await send('Page.captureScreenshot', { format: 'png' });
      fs.writeFileSync(path.join(out, mode + '-offers-' + width + '.png'), Buffer.from(offersShot.data, 'base64'));
      if (true) {
        state.comparison = await evaluate(`(() => {
          const buttons=[...document.querySelectorAll('.vectone-compare-button')];
          const count=()=>document.querySelector('[data-compare-count]').textContent;
          const counts=[]; buttons.slice(0,4).forEach(b=>{b.click();counts.push(count());});
          document.querySelector('[data-compare-open]').click();
          const modal=document.querySelector('.vectone-compare-modal');
          const open=!modal.hidden, items=modal.querySelectorAll('.vectone-compare-modal__item').length;
          const links=[...modal.querySelectorAll('a')].map(a=>a.getAttribute('href'));
          document.querySelector('.vectone-compare-modal__close').click();const close=modal.hidden;
          document.querySelector('[data-compare-open]').click();document.dispatchEvent(new KeyboardEvent('keydown',{key:'Escape'}));const escape=modal.hidden;
          document.querySelector('[data-compare-open]').click();document.querySelector('.vectone-compare-modal__backdrop').click();const overlay=modal.hidden;
          return {counts,open,items,links,close,escape,overlay};
        })()`);
      }
    }
    fs.writeFileSync(path.join(out, mode + '.json'), JSON.stringify(report, null, 2));
    console.log(JSON.stringify({ artifacts: out, errors: report.errors, widths: report.widths.map(({width,scrollWidth,products,featured,sections,compareButtons,comparison})=>({width,scrollWidth,products,featured,sections,compareButtons,comparison})) }, null, 2));
    await send('Browser.close');
  } finally { if (ws) ws.close(); proc.kill(); }
})().catch(e=>{console.error(e);process.exitCode=1;});
