import{T as f,x as _,f as g,a as l,w as d,o as b,b as t,t as r,u as o,d as p,n as h,e as v}from"./app-64491542.js";import{_ as w}from"./InputError-fa3e1639.js";import{_ as y,a as $}from"./TextInput-8835f8f7.js";import{_ as k,a as S}from"./SecondaryButton-4a877555.js";import{_ as V}from"./PrimaryButton-4c3adb2e.js";const C={class:"space-y-6"},x=["onSubmit"],B={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},E={class:"my-6 space-y-4"},N={class:"flex justify-end"},F={__name:"Edit",props:{show:Boolean,title:String,permission:Object},emits:["close"],setup(u,{emit:i}){const n=u,e=f({name:""}),c=()=>{var s;e.put(route("permission.update",(s=n.permission)==null?void 0:s.id),{preserveScroll:!0,onSuccess:()=>{i("close"),e.reset()},onError:()=>null,onFinish:()=>null})};return _(()=>{var s;n.show&&(e.errors={},e.name=(s=n.permission)==null?void 0:s.name)}),(s,a)=>(b(),g("section",C,[l(k,{show:n.show,onClose:a[2]||(a[2]=m=>i("close"))},{default:d(()=>[t("form",{class:"p-6",onSubmit:v(c,["prevent"])},[t("h2",B,r(s.lang().label.edit)+" "+r(n.title),1),t("div",E,[t("div",null,[l(y,{for:"name",value:s.lang().label.role},null,8,["value"]),l($,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:o(e).name,"onUpdate:modelValue":a[0]||(a[0]=m=>o(e).name=m),required:"",placeholder:s.lang().placeholder.name,error:o(e).errors.name},null,8,["modelValue","placeholder","error"]),l(w,{class:"mt-2",message:o(e).errors.name},null,8,["message"])])]),t("div",N,[l(S,{disabled:o(e).processing,onClick:a[1]||(a[1]=m=>i("close"))},{default:d(()=>[p(r(s.lang().button.close),1)]),_:1},8,["disabled"]),l(V,{class:h(["ml-3",{"opacity-25":o(e).processing}]),disabled:o(e).processing,onClick:c},{default:d(()=>[p(r(o(e).processing?s.lang().button.save+"...":s.lang().button.save),1)]),_:1},8,["class","disabled"])])],40,x)]),_:1},8,["show"])]))}};export{F as default};