

function SpaLoader(element){
    // spa loader to be updated nased on script
    let loader=` <div class="spa-loader">
 <svg fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="4" cy="12" r="3"><animate id="spinner_qFRN" begin="0;spinner_OcgL.end+0.25s" attributeName="cy" calcMode="spline" dur="0.6s" values="12;6;12" keySplines=".33,.66,.66,1;.33,0,.66,.33"/></circle><circle cx="12" cy="12" r="3"><animate begin="spinner_qFRN.begin+0.1s" attributeName="cy" calcMode="spline" dur="0.6s" values="12;6;12" keySplines=".33,.66,.66,1;.33,0,.66,.33"/></circle><circle cx="20" cy="12" r="3"><animate id="spinner_OcgL" begin="spinner_qFRN.begin+0.2s" attributeName="cy" calcMode="spline" dur="0.6s" values="12;6;12" keySplines=".33,.66,.66,1;.33,0,.66,.33"/></circle></svg>
      </div>
`;
    element.innerHTML=loader;
}
//  btn loader
function BtnLoader(element){
    // button loaders on submission to be updated based on script
    let loader=` <!--By Sam Herbert (@sherb), for everyone. More @ http://goo.gl/7AJzbL--><!--Todo: add easing--><svg viewBox="0 0 57 60" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="3"><circle cx="5" cy="50" r="5"><animate attributeName="cy" begin="0s" dur="2.2s" values="50;5;50;50" calcMode="linear" repeatCount="indefinite"/><animate attributeName="cx" begin="0s" dur="2.2s" values="5;27;49;5" calcMode="linear" repeatCount="indefinite"/></circle><circle cx="27" cy="5" r="5"><animate attributeName="cy" begin="0s" dur="2.2s" from="5" to="5" values="5;50;50;5" calcMode="linear" repeatCount="indefinite"/><animate attributeName="cx" begin="0s" dur="2.2s" from="27" to="27" values="27;49;5;27" calcMode="linear" repeatCount="indefinite"/></circle><circle cx="49" cy="50" r="5"><animate attributeName="cy" begin="0s" dur="2.2s" values="50;50;5;50" calcMode="linear" repeatCount="indefinite"/><animate attributeName="cx" from="49" to="49" begin="0s" dur="2.2s" values="49;5;27;49" calcMode="linear" repeatCount="indefinite"/></circle></g></g></svg>
     `;
 
    if(!element.classList.contains('active')){
        element.classList.add('active');
    }
    let svg_loader=element.querySelector('.svg-loader');
    if(!svg_loader){
      let div=document.createElement('div');
      div.classList.add('svg-loader');
      div.classList.add('row');
      div.classList.add('align-center');
      div.classList.add('g-10');
      div.innerHTML=loader;  
      
      element.appendChild(div);
    
    }
}
// action loader
function ActionLoader(){
    document.querySelector('.action-loader').classList.remove('display-none');
    document.body.classList.add('overflow-hidden');

}
    // action loader
function HideActionLoader(){
    document.querySelector('.action-loader').classList.add('display-none');
    document.body.classList.remove('overflow-hidden');

}
// wrap button raw text
function WrapBtnText(element) {
  // Go through all child nodes
  element.childNodes.forEach(node => {
    // Only target raw text nodes
    if (node.nodeType === Node.TEXT_NODE) {
      let text = node.textContent.trim();
      if (text.length > 0) {
        // Replace the text node with a span wrapping it
        let span = document.createElement('span');
        span.textContent = text;
        node.replaceWith(span);
      }
    }
  });
}
//  show ball loading
function BallLoad(){
    document.body.classList.add('loading');
}
//  hide ball loading
function HideBallLoad(){
   document.body.classList.remove('loading');
}
function IsJSONABLE(data){
    try{
      JSON.parse(data);
      return true;
    }catch{
        return false;
    }
}
// post request
async function PostRequest(event,element,callback=null,btn_text=null){
  try{
      event.preventDefault();
 let inputs=element.querySelectorAll('.inp.required');
 
 
 let isEmpty = false;

 if(inputs){
    
    
    inputs.forEach((input)=>{
         let cont=input.closest('.cont');
        //  FIRST REMOVE EMPTY STATE
         if(cont){
         
        
            cont.classList.remove('empty');
           
           }else{
          
             input.classList.remove('empty');
            
           }
        //    CHECK FOR EMPTY INPUTS THAT ARE REQUIRED

        if(input.value.trim() == ''){
            isEmpty=true;
          
           
        if(cont){
            cont.classList.add('empty');
            
        }else{
              input.classList.add('empty');
        }
        }

    });
 }
 
 if(!isEmpty){
    // loading state
   let post_btn=element.querySelector('button.post');
   if(post_btn){
    let data_text=post_btn.dataset.text;
    if(!data_text){
        post_btn.dataset.text=post_btn.innerHTML;
    }
     post_btn.classList.toggle('disabled');
     post_btn.innerHTML=btn_text ?? 'Processing...';
   }


    let inps=element.querySelectorAll('.input');
    let form=new FormData();
   
    inps.forEach((inp)=>{
       form.append(inp.name,inp.value);
    });
    // check for photos
    let files=element.querySelectorAll('input[type=file]');
    if(files){
        files.forEach((inp)=>{
            let file=inp.files[0];
            if(file){
                form.append(inp.name,file);
            }
        })
    }

    
    let response=await fetch(element.action,{
        method : 'POST',
        body : form
     });
     
     if(response.ok){
        let data=await response.text();
        
        if(IsJSONABLE(data)){
            let json=JSON.parse(data);
            CreateNotify(json.status,json.message);
        }else{
            CreateNotify('error',data);
        }
        if(callback !== null){
            callback(data,event);
        }
       if(post_btn){
         post_btn.innerHTML=post_btn.dataset.text;
        post_btn.classList.toggle('disabled');
       }
     }else{
        if(post_btn){
         post_btn.innerHTML=post_btn.dataset.text;
        post_btn.classList.toggle('disabled');
       }
        CreateNotify('error','Internal Error: ' + response.status + ' Error');
        
     }
     
 }
  }catch(error){
   HideActionLoader();
    CreateNotify('error',error.stack);
    element.querySelector('button').classList.remove('active');
  }
}

//  hide prompt
function HidePrompt(){
    let conts=document.querySelectorAll('.cont.required');
  if(conts){
      conts.forEach((cont)=>{
        let inp=cont.querySelector('.input');
        inp.addEventListener('focus',()=>{
            conts.forEach((required)=>{
                required.querySelector('.prompt').style.display="none";
            })
        })
    })
  }
}

// create notify
function CreateNotify(status,message){
    let notifies=document.querySelectorAll('.notify');
    if(notifies){
        notifies.forEach((notify)=>{
            notify.remove();
        })
    }
  let section=document.createElement('section');
  section.classList.add('notify');
  section.classList.add(status);

  section.innerHTML=` <div class="row g-5 w-full p-5 body space-between align-center">
           
             <div class="column m-right-auto g-5">
              <strong style="text-transform:capitalize;" class="desc">
            ${status}
        </strong>
            <div class="message">
            ${message}
        </div>
             </div>
        <div onclick="HideNotify()" class="pc-pointer m-bottom-auto no-select" style="font-size:2rem">&times;</div>
        </div>`;
       
       
       
       
        document.body.appendChild(section);
        let RemoveNotify=setTimeout(()=>{
            section.remove();
        },5000);
    
}
function HideNotify(){
  let notify= document.querySelector('.notify');
    if(notify){
     notify.remove();
    }
}
// create notify 2
function CreateNotify2(status,message,data=null){
    let notify2=document.createElement('div');
    notify2.classList.add('notify2');
    let icon;
    let btn_text;
    let btn_function;
    if(data == null){
      btn_text='Understood';
      btn_function=function(){
        notify2.remove();
      }
    }else{
        btn_text = data.btn_text;
        btn_function=data.btn_function;
    }

    if(status == 'success'){
        icon=`<div class="c-green h-70 w-70 bg-green-transparent column justify-center circle">
        <svg width="50" height="50" viewBox="0 0 24 24" fill="CurrentColor" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM16.0303 8.96967C16.3232 9.26256 16.3232 9.73744 16.0303 10.0303L11.0303 15.0303C10.7374 15.3232 10.2626 15.3232 9.96967 15.0303L7.96967 13.0303C7.67678 12.7374 7.67678 12.2626 7.96967 11.9697C8.26256 11.6768 8.73744 11.6768 9.03033 11.9697L10.5 13.4393L12.7348 11.2045L14.9697 8.96967C15.2626 8.67678 15.7374 8.67678 16.0303 8.96967Z" fill="CurrentColor"></path>
</svg>

    </div>`;
    }else{
        icon=` <div class="c-red h-70 w-70 bg-red-transparent column justify-center circle">
        <svg width="50" height="50" viewBox="0 0 24 24" fill="CurrentColor" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM8.96963 8.96965C9.26252 8.67676 9.73739 8.67676 10.0303 8.96965L12 10.9393L13.9696 8.96967C14.2625 8.67678 14.7374 8.67678 15.0303 8.96967C15.3232 9.26256 15.3232 9.73744 15.0303 10.0303L13.0606 12L15.0303 13.9696C15.3232 14.2625 15.3232 14.7374 15.0303 15.0303C14.7374 15.3232 14.2625 15.3232 13.9696 15.0303L12 13.0607L10.0303 15.0303C9.73742 15.3232 9.26254 15.3232 8.96965 15.0303C8.67676 14.7374 8.67676 14.2625 8.96965 13.9697L10.9393 12L8.96963 10.0303C8.67673 9.73742 8.67673 9.26254 8.96963 8.96965Z" fill="CurrentColor"></path>
</svg>


    </div>`;
    }
    notify2.innerHTML=`
    <section  style="z-index:90000" class="notify2 column p-10 bg-black-transparent justify-center pos-fixed top-0 left-0 bottom-0 right-0 z-index-9000">
<div class="w-full child align-center max-w-500 column br-10 p-10 g-10 bg-light">
    ${icon}
    <strong class="desc">${status}</strong>
    <span>${message}</span>
    <span></span>
<div class="w-full action-btn no-shrink br-10 clip-10 pointer no-select bg-primary primary-text p-10 h-50 row justify-center">${btn_text}</div>

</div>
</section>
    `;
    notify2.querySelector('.action-btn').onclick=function(){
        btn_function();
    }
     document.body.classList.add('overflow-hidden');
    document.body.appendChild(notify2);
}
// hide notify 2
function HideNotify2(){
    document.querySelector('.notify-2').remove();
    document.body.classList.remove('overflow-hidden');
}
// get request
async function GetRequest(event,url,element=null,callback=null){
    try{
        event.preventDefault();
        if(element !== null){
            // WrapBtnText(element);
            // element.classList.add('active');
            // BtnLoader(element);
            ActionLoader();
        }
        let response=await fetch(url);
        if(response.ok){
           
            if(element !== null){
            // element.classList.remove('active');
        }
             if(callback !== null){
                callback(await response.text(),event);
            }
            HideActionLoader();
        } else{
            CreateNotify('error',response.status + ' Error');
           if(element !== null){
            // element.classList.remove('active');
        }
        }
    }catch(error){
        HideActionLoader();
        CreateNotify('error',error.stack);
       if(element !== null){
            // element.classList.remove('active');
        }
    }
}
// search request
async function SearchRequest(event,element,url,result){
    event.preventDefault();
    if(element.value == ''){
        result.classList.add('display-none');
    }else{

       
        let response=await fetch(url);
        if(response.ok){
           
            result.innerHTML=await response.text();
             result.classList.remove('display-none');
        }else{
             result.classList.remove('display-none');
            result.innerHTML=` <a class="row no-u text-dim clip-10 align-center g-5 space-between p-10">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#708090" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216ZM80,108a12,12,0,1,1,12,12A12,12,0,0,1,80,108Zm96,0a12,12,0,1,1-12-12A12,12,0,0,1,176,108Zm-1.08,64a8,8,0,1,1-13.84,8c-7.47-12.91-19.21-20-33.08-20s-25.61,7.1-33.08,20a8,8,0,1,1-13.84-8c10.29-17.79,27.39-28,46.92-28S164.63,154.2,174.92,172Z"></path></svg>
                 <span class="m-right-auto">${response.status} Error</span>
                   </a>`
        }
    }
    
}
// copy
async function copy(data) {
    // Helper function for fallback copy (works on older iOS)
    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.top = '-9999px';
        textarea.style.left = '-9999px';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        
        textarea.select();
        textarea.setSelectionRange(0, text.length);
        
        let success = false;
        
            success = document.execCommand('copy');
        
        
        document.body.removeChild(textarea);
        return success;
    }
    
   
        // Try modern Clipboard API first (newer iPhones iOS 13.4+)
        if (navigator.clipboard && window.isSecureContext && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(data);
            CreateNotify('success', 'Copied successfully');
        } 
        // Fallback for older iPhones (iOS 9-13.3)
        else {
            const success = fallbackCopy(data);
            if (success) {
                CreateNotify('success', 'Copied successfully');
            }
        }
    
}
// show popup
function PopUp(data=null){
    if(data !== null){
        document.querySelector('.popup .child').innerHTML=data;
    }
    document.querySelector('.popup').classList.add('active');
    document.body.classList.add('overflow-hidden');
    document.body.style.overflow="hidden";
}
// hide popup
function HidePopUp(callback=null){
   try{
     document.querySelector('.popup').classList.remove('active');
    document.body.classList.remove('overflow-hidden');
    document.body.style.overflow="auto";
    callback?.();
   }catch(error){
    CreateNotify('error',error.stack);
   }
}
// slideup
function SlideUp(content=null){
    if(content !== null){
        document.querySelector('.slideup .child').innerHTML=content;
    }
    document.querySelector('.slideup').classList.add('active');
    document.body.classList.add('overflow-hidden');
}
//  hide side up
function HideSlideUp(){
      document.querySelector('.slideup').classList.remove('active');
   document.body.classList.remove('overflow-hidden');
}
// stop propagation
function StopPropagation(event){
    event.stopPropagation();
}
// Infinite lloading
function InfiniteLoading(){
  
  let observer=new IntersectionObserver((entries)=>{
    entries.forEach(async (entry)=>{
        if(entry.isIntersecting){
          //  observer.unobserve(entry.target);
            let url=entry.target.dataset.url;
           
           
           let response=await fetch(url);
           if(response.ok){
            let data=await response.text();
         entry.target.outerHTML=data;
        
           //entry.target.remove();
        InfiniteLoading();
           }
        }
    })
  })
//   observe
let element=document.querySelector('.infinite-loading');
if(element){
    observer.observe(element);
}
}
// preview photo
function PreviewPhoto(element,label){
    let file=element.files[0];
    
    if(file){
        label.children[0].style.display='none';
        label.style.backgroundImage=`url('${URL.createObjectURL(file)}')`;
        label.classList.remove('bg');

    }else{
        label.style.backgroundImage='';
        label.children[0].style.display='flex';
         label.classList.add('bg');
    }

}
// hide loading
function HideLoading(){
    let loading=document.querySelector(".loading-state");
    if(loading){
        loading.remove()
    }
        
   

}
// set vh
function SetWindowHeight(){
    let height=window.innerHeight;
    document.body.style.minHeight=height + 'px';
}
// remove empty class from inputs and conts

function UnEmpty(){
    let inps=document.querySelectorAll('.inp.required');
 //   alert(10)
    if(inps){
        inps.forEach((inp)=>{
           inp.addEventListener('focus',()=>{
             let cont=inp.closest('.cont');
            if(cont){
                cont.classList.remove('empty');
            }else{
                inp.classList.remove('empty');
            }
           })
        })
    }
}

// single page navigation
async function spa(event,url,element=null){
  try{
      // select main 
    let main=document.querySelector('main');
    //    add active class to the bottom nav
    if(element !== null){
        let navs=document.querySelectorAll('footer .nav');
        navs.forEach((nav)=>{
            nav.classList.remove('active');
        })
            element.classList.add('active');
        }
        // SpaLoader(main);
        ActionLoader();

        // fetch page
        let response=await fetch(url);
        if(response.ok){
            let data=await response.text();
            // create a div and put fetched data in it to be able to select
            let div=document.createElement('div');
            div.innerHTML=data;
            // remove old link with .css class if exists
            let old_css=document.querySelectorAll('link.css');
            if(old_css){
                old_css.forEach((css)=>{
                   css.remove();
                });
            }
            // remove old styles with .css class if exists
            let old_styles=document.querySelectorAll('style.css');
            if(old_styles){
                old_styles.forEach((style)=>{
                    style.remove();
                });
            }
            //  attach page link with css class if exists
            let new_css=div.querySelectorAll('link.css');
            if(new_css){
                new_css.forEach(async (css)=>{
                  await new Promise((resolve)=>{
                      let link=document.createElement('link');
                    link.classList.add('css');
                    link.href=css.href;
                    link.rel='stylesheet';
                    document.head.appendChild(link);
                    return resolve();
                  })
                })
            }
            //  attach page styles with .css tag if exists
            let new_styles=div.querySelectorAll('style.css');
            if(new_styles){
               new_styles.forEach(async (css)=>{
                await new Promise((resolve)=>{
                    let style=document.createElement('style');
                    style.classList.add('css');
                    style.textContent=css.textContent;
                    document.head.appendChild(style);
                    return resolve();
                })
               })
            }
            // redeclare my func to be an empty object
            window.MyFunc={};
            // attach new header
            await new Promise((resolve)=>{
                document.querySelector('header').outerHTML=div.querySelector('header').outerHTML;
                return resolve();
            })
            // attach new main
            await new Promise((resolve)=>{
                main.className=div.querySelector('main').className;
                main.innerHTML=div.querySelector('main').innerHTML;
                return resolve();
            })
            
            // remove old js if exists
            let old_js=document.querySelectorAll('script.js');
            
            if(old_js){
                old_js.forEach((js)=>{
                    js.remove();
                })
            }

            // attach new js if exists
            let new_js=div.querySelectorAll('script.js');
            if(new_js){
                new_js.forEach((js)=>{
                    let script=document.createElement('script');
                    script.classList.add('js');
                    if(js.src){
                        script.src=js.src;
                    }else{
                        script.textContent=js.textContent;
                    }
                    document.body.appendChild(script);
                })
            }
            
            // push state
            history.pushState({},'',url);
            document.title=div.querySelector('title').innerText;
            HideActionLoader();
            
            
        }else{
            main.innerHTML=`<div class="column flex-auto no-select g-5 justify-center">
        <svg height="100" width="100"  viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M370.1 737.5l13.7 7.3c15.6 8.2 33 15.6 51.3 21.1l14.7 4.6v68.7c0 16.5 11 20.2 20.2 20.2h82.4c9.2 0 20.2-3.7 20.2-20.2v-68.7l14.7-4.6c18.3-5.5 35.7-12.8 51.3-21.1l13.7-7.3 11 11c19.2 19.2 32.1 33 37.6 36.6 4.6 4.6 9.2 7.3 13.7 7.3 8.2 0 15.6-6.4 15.6-7.3l58.6-58.6c6.4-6.4 11.9-17.4 0-29.3l-48.5-48.5 7.3-13.7c8.2-15.6 15.6-33 21.1-51.3l4.6-14.7H842c16.5 0 20.2-11 20.2-20.2v-82.4c0-9.2-3.7-20.2-20.2-20.2h-68.7l-4.6-14.7c-6.4-17.4-12.8-34.8-22-50.4l-7.3-13.7 11-11 37.6-37.6c4.6-4.6 7.3-9.2 7.3-13.7 0-6.4-4.6-12.8-7.3-15.6L729.2 231c-1.8-1.8-8.2-7.3-15.6-7.3-4.6 0-9.2 1.8-13.7 7.3l-48.5 48.5-13.7-7.3c-15.6-8.2-33-15.6-50.4-21.1l-14.7-3.7v-69.6c0-17.4-14.7-20.2-21.1-20.2H470c-6.4 0-20.2 2.7-20.2 20.2v69.6l-14.7 4.6c-17.4 5.5-34.8 11.9-50.4 21.1l-13.7 7.3-49.4-49.4c-4.6-4.6-9.2-6.4-12.8-6.4-7.3 0-14.7 5.5-16.5 7.3l-58.6 57.7c-3.7 4.6-12.8 16.5 0 29.3l48.5 48.6-7.3 13.7c-8.2 15.6-15.6 33-21.1 50.4l-4.6 14.7h-68.7c-16.5 0.9-20.2 13.7-20.2 22v81.5c0 6.4 2.7 20.2 20.2 20.2h68.7l4.6 14.7c5.5 18.3 12.8 34.8 21.1 51.3l7.3 13.7-48.5 48.5c-11.9 11.9-4.6 23.8 0 29.3l57.7 57.7c1.8 0.9 9.2 6.4 15.6 6.4 4.6 0 8.2-1.8 12.8-6.4l50.3-47.7z" fill="#FFFFFF"></path><path d="M496.4 699.1h-0.8c-3.5-0.3-7-0.7-10.5-1.1-5.5-0.7-9.3-5.8-8.6-11.3s5.8-9.3 11.3-8.6c3.1 0.4 6.3 0.8 9.4 1 5.5 0.4 9.6 5.3 9.2 10.8-0.5 5.3-4.9 9.2-10 9.2z m39.8-1.1c-4.9 0-9.2-3.6-9.9-8.6-0.8-5.5 3.1-10.5 8.5-11.3 3.1-0.4 6.2-0.9 9.3-1.5 5.4-1.1 10.7 2.5 11.7 7.9s-2.5 10.7-7.9 11.7c-3.4 0.7-6.9 1.3-10.4 1.7-0.3 0.1-0.8 0.1-1.3 0.1z m-88.5-9.7c-1.2 0-2.3-0.2-3.5-0.6-3.3-1.2-6.6-2.6-9.8-4-5.1-2.2-7.4-8.1-5.1-13.2 2.2-5.1 8.1-7.4 13.2-5.1 2.9 1.3 5.8 2.5 8.8 3.6 5.2 1.9 7.8 7.7 5.9 12.9-1.6 3.9-5.4 6.4-9.5 6.4z m136.5-3.8c-3.8 0-7.5-2.2-9.2-6-2.2-5.1 0.1-11 5.1-13.2 2.9-1.3 5.7-2.6 8.5-4 4.9-2.5 10.9-0.5 13.4 4.4s0.5 10.9-4.4 13.4c-3.1 1.6-6.3 3.1-9.5 4.5-1.2 0.6-2.5 0.9-3.9 0.9z m-180.3-19.9c-2.1 0-4.1-0.6-5.9-1.9-2.8-2.1-5.6-4.3-8.3-6.5-4.3-3.5-4.9-9.8-1.4-14.1 3.5-4.3 9.8-4.9 14.1-1.4 2.4 2 4.9 4 7.5 5.8 4.5 3.3 5.4 9.5 2.1 14-2 2.7-5.1 4.1-8.1 4.1z m222.7-6.3c-2.9 0-5.7-1.2-7.7-3.6-3.5-4.3-2.9-10.6 1.3-14.1 2.4-2 4.8-4.1 7.1-6.2 4.1-3.7 10.4-3.5 14.1 0.6 3.7 4.1 3.5 10.4-0.6 14.1-2.6 2.4-5.2 4.7-7.9 6.9-1.8 1.6-4.1 2.3-6.3 2.3z m-258.5-28.4c-3 0-5.9-1.3-7.9-3.8-2.2-2.8-4.3-5.6-6.3-8.5-3.2-4.5-2-10.8 2.5-13.9 4.5-3.2 10.8-2 13.9 2.5 1.8 2.6 3.7 5.1 5.6 7.6 3.4 4.4 2.6 10.6-1.7 14-1.8 1.4-3.9 2.1-6.1 2.1z m292.2-8.2c-2 0-4-0.6-5.7-1.8-4.5-3.1-5.7-9.4-2.5-13.9 1.8-2.6 3.5-5.2 5.2-7.9 2.9-4.7 9.1-6.2 13.8-3.3 4.7 2.9 6.2 9.1 3.3 13.8-1.8 3-3.8 5.9-5.8 8.8-2 2.8-5.1 4.3-8.3 4.3z m-317.2-34.9c-3.9 0-7.7-2.3-9.3-6.2-1.3-3.2-2.6-6.6-3.7-9.9-1.8-5.2 1-10.9 6.2-12.7 5.2-1.8 10.9 1 12.7 6.2 1 3 2.1 5.9 3.3 8.8 2.1 5.1-0.4 11-5.5 13-1.1 0.5-2.4 0.8-3.7 0.8z m339.8-9.6c-1.1 0-2.2-0.2-3.2-0.5-5.2-1.8-8-7.5-6.2-12.7 1-3 2-6 2.8-9 1.5-5.3 7-8.4 12.3-6.9 5.3 1.5 8.4 7 6.9 12.3-1 3.4-2 6.8-3.1 10.1-1.5 4.1-5.4 6.7-9.5 6.7z m-352-38.7c-5 0-9.4-3.8-9.9-8.9-0.4-3.5-0.7-7-0.9-10.5-0.3-5.5 3.9-10.2 9.4-10.5 5.5-0.3 10.2 3.9 10.5 9.4 0.2 3.1 0.4 6.3 0.8 9.4 0.6 5.5-3.3 10.4-8.8 11-0.4 0.1-0.8 0.1-1.1 0.1z m361.5-10.2h-0.6c-5.5-0.3-9.7-5-9.4-10.5 0.2-3.1 0.3-6.3 0.3-9.4 0-5.5 4.5-10 10-10s10 4.5 10 10c0 3.5-0.1 7-0.3 10.5-0.3 5.3-4.7 9.4-10 9.4z m-360.2-39.6c-0.5 0-1.1 0-1.6-0.1-5.5-0.9-9.1-6-8.2-11.5 0.6-3.5 1.2-6.9 2-10.4 1.2-5.4 6.5-8.8 11.9-7.6 5.4 1.2 8.8 6.5 7.6 11.9-0.7 3.1-1.3 6.2-1.8 9.3-0.9 4.9-5.1 8.4-9.9 8.4z m357.9-0.8c-4.8 0-9-3.5-9.8-8.3-0.5-3.1-1.1-6.2-1.8-9.3-1.2-5.4 2.2-10.7 7.5-12 5.4-1.2 10.7 2.2 12 7.5 0.8 3.4 1.5 6.9 2 10.4 0.9 5.4-2.8 10.6-8.2 11.5-0.6 0.2-1.1 0.2-1.7 0.2z m-343.2-46.8c-1.4 0-2.9-0.3-4.3-1-5-2.4-7.1-8.3-4.8-13.3 1.5-3.2 3.1-6.3 4.8-9.4 2.6-4.9 8.7-6.7 13.6-4 4.9 2.6 6.7 8.7 4 13.6-1.5 2.8-2.9 5.6-4.3 8.4-1.6 3.6-5.2 5.7-9 5.7z m328.2-0.7c-3.7 0-7.3-2.1-9-5.7-1.3-2.8-2.8-5.7-4.3-8.4-2.6-4.8-0.9-10.9 4-13.6 4.8-2.6 10.9-0.9 13.6 4 1.7 3.1 3.3 6.2 4.8 9.4 2.4 5 0.3 11-4.7 13.3-1.5 0.7-2.9 1-4.4 1z m-300.9-41.1c-2.3 0-4.7-0.8-6.6-2.5-4.2-3.6-4.6-9.9-1-14.1 2.3-2.6 4.7-5.3 7.1-7.8 3.8-4 10.2-4.1 14.1-0.2 4 3.8 4.1 10.2 0.2 14.1-2.2 2.2-4.3 4.6-6.4 7-1.8 2.4-4.6 3.5-7.4 3.5z m273.6-0.5c-2.8 0-5.5-1.2-7.5-3.4-2.1-2.4-4.2-4.7-6.4-6.9-3.9-4-3.8-10.3 0.2-14.1 3.9-3.9 10.3-3.8 14.1 0.2 2.4 2.5 4.9 5.1 7.2 7.7 3.6 4.2 3.2 10.5-0.9 14.1-2.1 1.6-4.4 2.4-6.7 2.4z m-236.1-32.3c-3.3 0-6.4-1.6-8.4-4.5-3-4.6-1.7-10.8 2.9-13.8 2.9-1.9 5.9-3.8 9-5.5 4.8-2.8 10.9-1.1 13.7 3.6 2.8 4.8 1.2 10.9-3.6 13.7-2.7 1.6-5.4 3.2-8 5-1.8 1-3.7 1.5-5.6 1.5z m198.4-0.3c-1.9 0-3.8-0.5-5.4-1.6-2.6-1.7-5.3-3.4-8.1-4.9-4.8-2.8-6.4-8.9-3.7-13.7s8.9-6.4 13.7-3.7c3 1.7 6.1 3.6 9 5.5 4.6 3 5.9 9.2 2.9 13.8-1.9 3-5.1 4.6-8.4 4.6z m-153.4-21c-4.3 0-8.2-2.7-9.5-7-1.7-5.3 1.3-10.9 6.5-12.5 3.3-1.1 6.8-2 10.1-2.9 5.4-1.4 10.8 1.9 12.2 7.2 1.4 5.4-1.9 10.8-7.2 12.2-3 0.8-6.1 1.6-9.1 2.6-1 0.2-2 0.4-3 0.4zM565 345c-1 0-2-0.1-3-0.5-3-0.9-6-1.8-9.1-2.5-5.4-1.3-8.6-6.8-7.3-12.1 1.3-5.4 6.8-8.6 12.1-7.3 3.4 0.9 6.8 1.8 10.2 2.8 5.3 1.6 8.2 7.2 6.6 12.5-1.3 4.3-5.2 7.1-9.5 7.1z m-59.2-8.1c-5.4 0-9.8-4.3-10-9.7-0.2-5.5 4.2-10.1 9.7-10.3 3.5-0.1 7.1-0.1 10.5 0 5.5 0.1 9.9 4.7 9.7 10.3-0.1 5.4-4.6 9.7-10 9.7h-0.3c-3.1-0.1-6.3-0.1-9.5 0h-0.1z" fill="#005BFF"></path><path d="M552.4 879.1H470c-24 0-40.2-16.1-40.2-40.2v-54l-0.5-0.2c-18.8-5.7-37.2-13.2-54.8-22.5l-1.1-0.6-39.7 37.6c-5.3 5.2-13.8 12.1-26.8 12.1-10.9 0-20.1-5.7-24.5-8.5-0.2-0.1-0.3-0.2-0.5-0.3l-2.4-1.2-60.6-60.6-0.6-0.7c-15.2-18.2-14.7-40.3 1.2-56.3l38.1-38.1-0.6-1.1c-7-13.9-15.8-32.6-22.3-54.4l-0.2-0.5h-54c-27.6 0-40.2-20.8-40.2-40.2V468c0-19.8 12.1-40.5 39-42h55.1l0.2-0.6c5.8-18.4 13.6-36.9 22.5-53.8l0.4-0.7-38.1-38.1c-6-6-23.9-27.9-1.5-56l0.7-0.9 59.5-58.5c1.4-1.4 13.9-13.1 30.5-13.1 9.5 0 19.1 4.4 27 12.3l39 39 0.3-0.2c18.4-10.8 38.6-17.8 54.1-22.7l0.7-0.2v-54.9c0-27.6 20.8-40.2 40.2-40.2h81.5c5 0 14.6 0.9 23.7 6.7 11.2 7.2 17.3 19 17.3 33.4v54l0.7 0.2c18.3 5.8 36.9 13.5 53.7 22.5l0.7 0.4 37.5-37.5c9.8-11.3 20.9-13.8 28.4-13.8 15.3 0 26.6 10.1 29.7 13.2l58.6 58.6c6.4 6.4 13.2 17.5 13.2 29.7 0 9.8-4.4 19.1-13.2 27.9l-38.1 38.1 0.2 0.3c10 17 16.7 35.4 23.3 53.2l0.5 1.6h54c24 0 40.2 16.1 40.2 40.2v82.4c0 24-16.1 40.2-40.2 40.2h-54l-0.2 0.5c-5.7 18.8-13.2 37.3-22.5 54.8l-0.4 0.7 38.1 38.1c9.7 9.7 14.2 21.6 12.5 33.6-1.2 8.7-5.5 17-12.5 24L745.3 798c-7.1 9-20.5 14.2-30.8 14.2-9.3 0-18.2-4-26.6-11.9-5.2-3.9-11.7-10.5-24.5-23.4-4.3-4.3-9.1-9.2-14.3-14.5l-0.5-0.5-0.7 0.4c-17.6 9.3-36 16.9-54.8 22.5l-0.5 0.2v54c0 24-16.2 40.1-40.2 40.1z m-82.4-38z m-0.2-2h82.6V755.5l28.9-9c16.4-4.9 32.4-11.5 47.7-19.6l26.8-14.3 21.5 21.5c5.4 5.4 10.2 10.3 14.5 14.7 8.5 8.7 17.4 17.6 20 19.5l1.7 1.1 1.3 1.4 0.5 0.5 59.1-59.1c0.2-0.2 0.3-0.4 0.5-0.5-0.1-0.2-0.3-0.3-0.5-0.5l-59-59 14.3-26.8c8.1-15.2 14.6-31.3 19.6-47.6l0.1-0.2 9-28.7H842v-82.5-0.1H758.4l-8.8-28.2c-6.2-16.9-12.1-32.8-20.3-46.7l-0.4-0.7-14.3-26.8 59-59 0.5-0.5c-0.2-0.2-0.3-0.4-0.5-0.6L715 245.2c-0.2-0.1-0.3-0.3-0.6-0.5L655 303.9l-26.8-14.3c-14.5-7.7-30.6-14.4-46.4-19.5l-29.2-7.3v-85.2-0.1c-0.4-0.1-0.7-0.1-1.1-0.1H470h-0.1V261.8l-28.7 9c-13.6 4.3-31.2 10.4-46.2 19.2l-0.7 0.4-26.8 14.3-59.7-59.7c-0.6 0.3-1.1 0.7-1.5 1l-57.5 56.6c-0.5 0.6-0.8 1.1-1.1 1.5l0.3 0.3 59 59-14.3 26.8c-7.8 14.7-14.6 30.9-19.6 47l-8.9 28.6h-82.7c-0.2 0-0.4 0.1-0.6 0.1-0.1 0.5-0.2 1.1-0.2 1.9v81.6h83.5l9 28.9c4.4 14.6 10.5 29.4 19.7 47.9l14.2 26.6-59 59-0.3 0.3c0.2 0.4 0.5 0.9 1 1.5l55.2 55.2c0.6 0.3 1.4 0.9 2.3 1.3l60.8-57.5 26.4 14.1c15.2 8.1 31.3 14.6 47.6 19.6l0.2 0.1 28.7 9V839c-0.2 0-0.2 0.1-0.2 0.1z m-187.4-36.3z m-35.5-89.7z m529.1-0.3zM246.8 303.3z m59.6-57.5l-0.2 0.2c0.1-0.1 0.2-0.1 0.2-0.2z m3-1.5z m-3.2-0.4z m406.5-0.6z m3.4-0.6z" fill="#005BFF"></path><path d="M655.5 607.1l-11-10.2 3.6-6.8c26.9-50.8 17.1-114.6-23.8-155.1-33.6-33.3-82.1-45.7-127-33.5l78.8 78c18.1 17.9 18.2 47.2 0.3 65.3l-28 28.3c-8.7 8.8-20.2 13.6-32.6 13.7h-0.2c-12.2 0-23.8-4.7-32.5-13.4l-78.8-78c-11.7 45 1.2 93.4 34.8 126.7 40.9 40.5 104.8 49.6 155.3 22.2l6.8-3.7 182.1 192.8c5.6 6 13.2 9.3 21.4 9.4 8.2 0.1 15.9-3.1 21.6-8.9l13.8-13.9c5.8-5.8 8.8-13.5 8.7-21.7-0.2-8.2-3.6-15.8-9.6-21.3l-180-166.6-3.7-3.3z" fill="#E6E6E6"></path><path d="M669.2 592.5c12.3-26.6 16.8-56.8 12.5-85.9 4.3 29.2-0.2 59.4-12.5 85.9zM866.9 785.5c0.4 1.6 0.8 3.2 1.1 4.9-0.3-1.7-0.6-3.3-1.1-4.9zM672.7 595.8l-3.5-3.3zM655.5 607.1l3.6 3.4-3.6-3.4zM655.5 607.1l-11-10.2 11 10.2z" fill="#005BFF"></path><path d="M868 790.4c-0.3-1.6-0.7-3.3-1.1-4.9-0.3-1.1-0.6-2.2-1-3.3-2.7-7.5-7.1-14.3-13.1-19.9l-180-166.6-3.5-3.2c12.3-26.6 16.8-56.8 12.5-85.9-4.8-32.8-19.8-62.4-43.4-85.8-43.1-42.6-107.2-55.5-163.4-32.8l-14.1 5.7 101.1 100c5 4.9 7.7 11.5 7.8 18.5 0 7-2.7 13.6-7.6 18.5l-28 28.3c-4.9 5-11.5 7.7-18.5 7.8h-0.1c-6.9 0-13.5-2.7-18.4-7.6L396 459.2l-5.5 14.2c-22.1 56.4-8.5 120.4 34.6 163 23.6 23.4 53.4 38 86.2 42.5 29.2 3.9 59.3-0.8 85.8-13.4l171.7 181.8c9.4 9.9 22.1 15.5 35.7 15.6h0.6c13.4 0 26-5.2 35.5-14.8l13.8-13.9c9.6-9.7 14.7-22.6 14.4-36.2-0.1-2.6-0.3-5.2-0.8-7.6z m-63.3 52.5c-8.2-0.1-15.8-3.4-21.4-9.4L601.2 640.7l-6.8 3.7c-50.5 27.4-114.4 18.3-155.3-22.2-33.6-33.3-46.5-81.6-34.8-126.7l78.8 78c8.7 8.6 20.2 13.4 32.5 13.4h0.2c12.3-0.1 23.9-4.9 32.6-13.7l28-28.3c17.9-18.1 17.8-47.4-0.3-65.3l-78.8-78c44.9-12.2 93.4 0.2 127 33.5 40.9 40.5 50.7 104.2 23.8 155.1l-3.6 6.8 11 10.2 3.6 3.4 180 166.6c6 5.6 9.4 13.1 9.6 21.3 0.2 8.2-2.9 15.9-8.7 21.7L826.3 834c-5.7 5.8-13.4 8.9-21.6 8.9z" fill="#005BFF"></path><path d="M838.5 772.8c-2.4 0-4.9-0.9-6.8-2.7L668.1 618.7c-4.1-3.8-4.3-10.1-0.5-14.1 3.8-4.1 10.1-4.3 14.1-0.5l163.6 151.4c4.1 3.8 4.3 10.1 0.5 14.1-2 2.1-4.7 3.2-7.3 3.2z" fill="#06F3FF"></path></g></svg>
        <span class="text-small text-dim">Oops something went wrong while loading the page.Please try again</span>
        <span class="text-light">${response.status} Error</span>
           <span onclick="spa(event,'${url}',this)" class="c-primary pointer row align-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="rgb(100,255,100)" viewBox="0 0 256 256"><path d="M228,128a100,100,0,0,1-98.66,100H128a99.39,99.39,0,0,1-68.62-27.29,12,12,0,0,1,16.48-17.45,76,76,0,1,0-1.57-109c-.13.13-.25.25-.39.37L54.89,92H72a12,12,0,0,1,0,24H24a12,12,0,0,1-12-12V56a12,12,0,0,1,24,0V76.72L57.48,57.06A100,100,0,0,1,228,128Z"></path></svg>
            Try Again</span>
     </div>`;
     HideActionLoader();
        }
  }catch(error){
    CreateNotify('error',error.stack);
  }
}
// toggle nav group
function ToggleNavGroup(element){
    let group=element.closest('.group');
    if(group.classList.contains('active')){
 group.classList.remove('active');
    }else{
         group.classList.add('active');
    }
   
}
// toggle nav
function ToggleNav(){
    document.querySelector('nav').classList.add('active');
}
// Hide nav
function HideNav(){
    document.querySelector('nav').classList.remove('active');
}
// auto fill
function AutoFill(val,input,element){
   // alert(10)
   input.value=val;
   if(element !== null){
    element.classList.add('active');
   }


}
// calling functions


window.addEventListener('load',()=>{
       HideLoading();
    SetWindowHeight();
    UnEmpty();
    
});
 

