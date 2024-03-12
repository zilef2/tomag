import{k as S,T as b,C as j,x as C,f as t,a as d,w as _,o as i,b as c,t as p,F as E,j as $,u as a,d as v,n as F,e as U}from"./app-64491542.js";import{_ as g}from"./InputError-fa3e1639.js";import{_ as y,a as h}from"./TextInput-8835f8f7.js";import{_ as B,a as M}from"./SecondaryButton-4a877555.js";import{_ as N}from"./PrimaryButton-4c3adb2e.js";import{C as O}from"./vue-select-d9b12732.js";import"./vue-datepicker-265c879c.js";const q={class:"space-y-6"},x={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},T={class:"grid grid-cols-1 md:grid-cols-2 gap-6"},z={key:0,id:"SelectVue"},A={name:"labelSelectVue"},D={key:1,id:"SelectVue"},L={key:2,class:""},P={class:"my-8 flex justify-end"},W={__name:"Edit",props:{show:Boolean,title:String,generica:Object,titulos:Object,losSelect:Object},emits:["close"],setup(V,{emit:m}){const o=V,u=S({printForm:[]}),f=o.titulos.map(l=>l.order),s=b({...Object.fromEntries(f.map(l=>[l,""]))});j(()=>{if(o.numberPermissions>9){const l=Math.floor(Math.random()*9+1);s.nombre="admin orden trabajo "+l,s.codigo=l}}),o.titulos.forEach(l=>{u.printForm.push({idd:l.order,label:l.label,type:l.type,value:s[l.order]})}),C(()=>{o.show&&(s.errors={},o.titulos.forEach(l=>{s[l.order]=o.generica[l.order]}))});const k=()=>{var l;s.put(route("generic.update",(l=o.generica)==null?void 0:l.id),{preserveScroll:!0,onSuccess:()=>{m("close"),s.reset()},onError:()=>null,onFinish:()=>null})};return(l,n)=>(i(),t("section",q,[d(B,{show:o.show,onClose:n[2]||(n[2]=e=>m("close"))},{default:_(()=>[c("form",{class:"p-6",onSubmit:n[1]||(n[1]=U((...e)=>l.create&&l.create(...e),["prevent"]))},[c("h2",x,p(l.lang().label.edit)+" "+p(o.title),1),c("div",T,[(i(!0),t(E,null,$(u.printForm,(e,w)=>(i(),t("div",{key:w},[e.type=="id"?(i(),t("div",z,[c("label",A,p(e.label),1),d(a(O),{options:u[e.idd],label:"title",modelValue:a(s)[e.idd],"onUpdate:modelValue":r=>a(s)[e.idd]=r,value:u[e.idd][o.generica.generic_id]},null,8,["options","modelValue","onUpdate:modelValue","value"]),d(g,{class:"mt-2",message:a(s).errors[e.idd]},null,8,["message"])])):e.type=="time"?(i(),t("div",D,[d(y,{for:e.label,value:l.lang().label[e.label]},null,8,["for","value"]),d(h,{id:e.idd,type:e.type,class:"mt-1 block w-full",modelValue:a(s)[e.idd],"onUpdate:modelValue":r=>a(s)[e.idd]=r,required:"",placeholder:e.label,error:a(s).errors[e.idd],step:"3600"},null,8,["id","type","modelValue","onUpdate:modelValue","placeholder","error"]),d(g,{class:"mt-2",message:a(s).errors[e.idd]},null,8,["message"])])):(i(),t("div",L,[d(y,{for:e.label,value:l.lang().label[e.label]},null,8,["for","value"]),d(h,{id:e.idd,type:e.type,class:"mt-1 block w-full",modelValue:a(s)[e.idd],"onUpdate:modelValue":r=>a(s)[e.idd]=r,required:"",placeholder:e.label,error:a(s).errors[e.idd]},null,8,["id","type","modelValue","onUpdate:modelValue","placeholder","error"]),d(g,{class:"mt-2",message:a(s).errors[e.idd]},null,8,["message"])]))]))),128))]),c("div",P,[d(M,{disabled:a(s).processing,onClick:n[0]||(n[0]=e=>m("close"))},{default:_(()=>[v(p(l.lang().button.close),1)]),_:1},8,["disabled"]),d(N,{class:F(["ml-3",{"opacity-25":a(s).processing}]),disabled:a(s).processing,onClick:k},{default:_(()=>[v(p(a(s).processing?l.lang().button.save+"...":l.lang().button.save),1)]),_:1},8,["class","disabled"])])],32)]),_:1},8,["show"])]))}};export{W as default};
