import{T as f,f as g,a as d,w as m,o as _,b as o,t,d as l,u as r,n as b,e as h}from"./app-64491542.js";import{_ as y}from"./DangerButton-c594852b.js";import{_ as w,a as x}from"./SecondaryButton-4a877555.js";const k={class:"space-y-6"},S=["onSubmit"],v={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},C={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},$={class:"mt-6 flex justify-end"},D={__name:"Delete",props:{show:Boolean,title:String,generica:Object},emits:["close"],setup(p,{emit:i}){const a=p,s=f({}),u=()=>{var e;s.delete(route("centrotrabajo.destroy",(e=a.generica)==null?void 0:e.id),{preserveScroll:!0,onSuccess:()=>{i("close"),s.reset()},onError:()=>null,onFinish:()=>null})};return(e,n)=>(_(),g("section",k,[d(w,{show:a.show,onClose:n[1]||(n[1]=c=>i("close")),maxWidth:"lg"},{default:m(()=>{var c;return[o("form",{class:"p-6",onSubmit:h(u,["prevent"])},[o("h2",v,t(e.lang().label.delete)+" "+t(a.title),1),o("p",C,[l(t(e.lang().label.delete_confirm)+" ",1),o("b",null,t((c=a.generica)==null?void 0:c.name),1),l("? ")]),o("div",$,[d(x,{disabled:r(s).processing,onClick:n[0]||(n[0]=B=>i("close"))},{default:m(()=>[l(t(e.lang().button.close),1)]),_:1},8,["disabled"]),d(y,{class:b(["ml-3",{"opacity-25":r(s).processing}]),disabled:r(s).processing,onClick:u},{default:m(()=>[l(t(r(s).processing?e.lang().button.delete+"...":e.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,S)]}),_:1},8,["show"])]))}};export{D as default};
