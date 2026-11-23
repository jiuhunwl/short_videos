import assert from 'node:assert/strict';
import {embeddedState,extractKuaishou,extractXhs,formatXhs,parsePlatform} from '../cloudflare/platforms.mjs';
assert.equal(embeddedState('<script>window.__INITIAL_STATE__={"a":undefined,"b":"undefined and } text"};</script>','__INITIAL_STATE__').b,'undefined and } text');
const ks='<script>window.INIT_STATE='+JSON.stringify({tusjoh1:{photo:{caption:'快手图集',userName:'作者',ext_params:{atlas:{list:['path/a.jpg','path/b.jpg']}},music:{audioUrls:[{url:'https://txmov2.a.kwimgs.com/music.mp3'}]}}}})+'</script>';
assert.equal(extractKuaishou(ks,'https://www.kuaishou.com/short-video/1').images.length,2);
const video='<script>window.INIT_STATE='+JSON.stringify({tusjoh1:{photo:{caption:'快手视频',mainMvUrls:[{url:'https://v.a.yximgs.com/test.mp4'}]}}})+'</script>';
assert.equal(extractKuaishou(video,'https://www.kuaishou.com/short-video/1').type,'video');
const note={title:'小红书视频',type:'video',user:{userId:'00123',nickname:'作者'},imageList:[{urlDefault:'https://sns-img.xhscdn.com/cover.jpg'}],video:{media:{stream:{h265:[{masterUrl:'https://sns-video.xhscdn.com/hevc.mp4'}],h264:[{masterUrl:'https://sns-video.xhscdn.com/avc.mp4'}]}}}};
const xhs='<script>window.__INITIAL_STATE__='+JSON.stringify({note:{noteDetailMap:{abc:{note}}}})+'</script>';
const result=extractXhs(xhs,'https://www.xiaohongshu.com/explore/abc?xsec_token=keep');assert.equal(result.url,'https://sns-video.xhscdn.com/avc.mp4');assert.equal(result.images.length,0);assert.equal(result.author.id,'00123');
let requested=[];const mock=async(url,options)=>{requested.push(url);assert.equal(options.redirect,'manual');return requested.length===1?new Response(null,{status:302,headers:{Location:'https://www.xiaohongshu.com/explore/abc?xsec_token=keep'}}):new Response(xhs);};
assert.equal((await parsePlatform('https://xhslink.cn/o/test','xiaohongshu',mock)).code,200);assert.ok(requested[1].includes('xsec_token=keep'));
let overseasCalls=0;const overseas=async()=>++overseasCalls===1?new Response(null,{status:302,headers:{Location:'https://www.rednote.com/explore/abc?xsec_token=keep'}}):new Response(xhs);
assert.equal((await parsePlatform('https://xhslink.cn/o/test','xiaohongshu',overseas)).code,200);
const loginPage=async(url)=>new URL(url).pathname==='/login'?new Response('<html><title>登录</title></html>'):new Response(null,{status:302,headers:{Location:'https://www.rednote.com/login'}});
const limited=await parsePlatform('https://xhslink.cn/o/test','xiaohongshu',loginPage);assert.equal(limited.code,403);assert.equal(limited.diagnostics,undefined);
const rejected=await parsePlatform('https://v.kuaishou.com/test','kuaishou',async()=>new Response(null,{status:302,headers:{Location:'http://127.0.0.1/private'}}));assert.equal(rejected.code,502);
assert.equal((await parsePlatform('https://kuaishou.com.evil.test/','kuaishou',mock)).code,400);
console.log('PASS: Kuaishou video/gallery, XHS H.264 selection, embedded JSON safety, signed queries and redirect restrictions');

// Browser Run returns a JSON envelope around the rendered Rednote mobile HTML.
const mobileHtml='<script>window.__INITIAL_STATE__='+JSON.stringify({noteData:{data:{noteData:{...note,type:'normal',imageList:Array.from({length:8},(_,i)=>({urlDefault:'https://sns-img.xhscdn.com/'+i+'.jpg'}))}}}})+'</script>';
let renders=0;
const browser={quickAction:async(action,options)=>{
  renders++;assert.equal(action,'content');assert.equal(options.url,'https://xhslink.cn/o/test');
  assert.equal(options.gotoOptions.timeout,20000);assert.ok(options.rejectResourceTypes.includes('media'));
  assert.equal(options.cookies,undefined);
  return Response.json({success:true,result:mobileHtml});
}};
const rendered=await parsePlatform('https://xhslink.cn/o/test','xiaohongshu',loginPage,browser);
assert.equal(rendered.code,200);assert.equal(rendered.data.images.length,8);assert.equal(renders,1);
assert.equal((await parsePlatform('https://xhslink.cn/o/test','xiaohongshu',loginPage,{quickAction:async()=>{throw new Error('quota exceeded');}})).code,403);
assert.equal((await parsePlatform('https://xhslink.cn/o/test','xiaohongshu',loginPage,{quickAction:async()=>Response.json({success:true,result:'<html>Login</html>'})})).code,403);
await parsePlatform('https://www.xiaohongshu.com/explore/abc','xiaohongshu',async()=>new Response(xhs),browser);assert.equal(renders,1);
await parsePlatform('https://127.0.0.1/private','xiaohongshu',loginPage,browser);assert.equal(renders,1);
console.log('PASS: cloud browser envelope, eight-image gallery, quota/login fallback, no redundant rendering or untrusted navigation');

// A valid video on Douyin's newer CDN must survive download registration.
const {registerMedia,mediaAllowed}=await import('../cloudflare/media.mjs');
const db={prepare:()=>({bind:(...values)=>values}),batch:async()=>{}};
for(const host of ['v5-dy-ov-experiment.zjcdn.com','v3-dy-o.zjcdn.com','4b19a02d-9.sjxydc.com']){
  const registered=await registerMedia(db,1,{type:'video',title:'她不一样',url:'https://'+host+'/sample.mp4',cover:'https://p3.douyinpic.com/cover.jpg'});
  assert.deepEqual(registered.downloads.map(a=>a.kind),['video','cover']);
  assert.equal(mediaAllowed('https://'+host+'.evil.test/sample.mp4'),false);
}
assert.equal(mediaAllowed('https://unrelated.zjcdn.com/sample.mp4'),false);
console.log('PASS: Douyin video CDN registration and spoofed/unrelated host rejection');

const qualityPhoto={width:720,height:1280,mainMvUrls:[{url:'https://v.a.yximgs.com/720.mp4'}],manifest:{adaptationSet:[{representation:[
  {url:'https://v.a.yximgs.com/540.mp4',width:540,height:960,avgBitrate:9000000},
  {url:'https://v.a.yximgs.com/1080-low.mp4',width:1080,height:1920,avgBitrate:1000000},
  {url:'https://v.a.yximgs.com/1080-high.mp4',width:1080,height:1920,avgBitrate:2000000}
]}]}};
const qualityKs=extractKuaishou('window.INIT_STATE='+JSON.stringify({tusjoh1:{photo:qualityPhoto}}),'https://www.kuaishou.com/short-video/1');
assert.equal(qualityKs.url,'https://v.a.yximgs.com/1080-high.mp4');assert.equal(qualityKs.quality,'1080×1920');
const qualityNote={type:'video',video:{media:{stream:{h264:[{masterUrl:'https://sns-video.xhscdn.com/720.mp4',width:720,height:1280,videoBitrate:9000000}],h265:[{masterUrl:'https://sns-video.xhscdn.com/4k.mp4',width:2160,height:3840,videoBitrate:5000000}]}}}};
assert.equal(formatXhs(qualityNote).url,'https://sns-video.xhscdn.com/4k.mp4');assert.equal(formatXhs(qualityNote).quality,'2160×3840');
qualityNote.video.consumer={originVideoKey:'/original.mp4'};
assert.equal(formatXhs(qualityNote).url,'https://sns-video-bd.xhscdn.com/original.mp4');assert.equal(formatXhs(qualityNote).quality,'original');
console.log('PASS: Kuaishou resolution/bitrate ranking and XHS cross-codec/original selection');
