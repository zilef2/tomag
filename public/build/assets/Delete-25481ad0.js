import{T as f,f as _,a as c,w as m,o as g,b as o,t,d as n,u as r,n as b,e as h}from"./app-64491542.js";import{_ as y}from"./DangerButton-c594852b.js";import{_ as w,a as x}from"./SecondaryButton-4a877555.js";const k={class:"space-y-6"},S=["onSubmit"],v={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},C={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},$={class:"mt-6 flex justify-end"},D={__name:"Delete",props:{show:Boolean,title:String,role:Object},emits:["close"],setup(p,{emit:i}){const l=p,s=f({}),u=()=>{var e;s.delete(route("role.destroy",(e=l.role)==null?void 0:e.id),{preserveScroll:!0,onSuccess:()=>{i("close"),s.reset()},onError:()=>null,onFinish:()=>null})};return(e,a)=>(g(),_("section",k,[c(w,{show:l.show,onClose:a[1]||(a[1]=d=>i("close")),maxWidth:"lg"},{default:m(()=>{var d;return[o("form",{class:"p-6",onSubmit:h(u,["prevent"])},[o("h2",v,t(e.lang().label.delete)+" "+t(l.title),1),o("p",C,[n(t(e.lang().label.delete_confirm)+" ",1),o("b",null,t((d=l.role)==null?void 0:d.name),1),n("? ")]),o("div",$,[c(x,{disabled:r(s).processing,onClick:a[0]||(a[0]=B=>i("close"))},{default:m(()=>[n(t(e.lang().button.close),1)]),_:1},8,["disabled"]),c(y,{class:b(["ml-3",{"opacity-25":r(s).processing}]),disabled:r(s).processing,onClick:u},{default:m(()=>[n(t(r(s).processing?e.lang().button.delete+"...":e.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,S)]}),_:1},8,["show"])]))}};export{D as default};
