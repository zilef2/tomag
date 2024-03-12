import{k as B,K as P,l as E,O as A,T as D,x as U,m as F,f as g,a as l,u as i,w as y,F as k,p as T,o as d,Z as K,b as e,q as m,v as h,d as $,t as o,c as u,g as w,j as L,s as M}from"./app-64491542.js";import{_ as Z,r as z}from"./AuthenticatedLayout-f13e9df2.js";import{_ as G}from"./Breadcrumb-8af54545.js";import{a as H}from"./TextInput-8835f8f7.js";import{_ as J}from"./PrimaryButton-4c3adb2e.js";import{_ as Q,r as R}from"./InfoButton-b50dc442.js";import{_ as W}from"./SelectInput-457de6e4.js";import{_ as C}from"./DangerButton-c594852b.js";import{r as O,a as p,_ as X}from"./Pagination-85722726.js";import Y from"./Create-50e09a8a.js";import ee from"./Edit-a729d9d3.js";import te from"./Delete-da5590d9.js";import se from"./DeleteBulk-bd586b5a.js";import{_ as re}from"./Checkbox-ff0406d7.js";import{C as ae}from"./global-8f0e871b.js";import"./ApplicationLogo-194673e3.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./index-0390d3e5.js";import"./InputError-fa3e1639.js";import"./SecondaryButton-4a877555.js";import"./vue-datepicker-265c879c.js";/* empty css             */import"./vue-select-d9b12732.js";const le={class:"space-y-4"},oe={class:"px-4 sm:px-0"},ne={class:"rounded-lg overflow-hidden w-fit"},ie={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},de={class:"flex justify-between p-2"},ce={class:"flex space-x-2"},pe={class:"overflow-x-auto scrollbar-table"},me={class:"w-full"},ue={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},fe={class:"dark:bg-gray-900/50 text-left"},ye={class:"px-2 py-4 text-center"},he=e("th",{class:"px-2 py-4 text-center"},"#",-1),be={class:"flex justify-between items-center"},_e={class:"px-2 py-4"},ge={class:"flex justify-between items-center"},we={class:"flex justify-between items-center"},xe={class:"flex justify-between items-center"},ve={class:"flex justify-between items-center"},ke={class:"flex justify-between items-center"},$e={class:"flex justify-between items-center"},Ce=e("th",{class:"px-2 py-4"},"Accion",-1),Oe={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},je=["value"],Se={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},Ie={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ve={class:"flex justify-start items-center"},Ne={class:"flex justify-start items-center text-sm text-gray-600"},qe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Be={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Pe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ee={class:"whitespace-nowrap text-center py-4 px-2 sm:py-3"},Ae={class:"whitespace-nowrap py-4 px-2 sm:py-3"},De={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ue={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Fe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Te={class:"flex justify-center items-center"},Ke={class:"rounded-md overflow-hidden"},Le={class:"flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700"},mt={__name:"Index",props:{title:String,filters:Object,users:Object,roles:Object,breadcrumbs:Object,perPage:Number,numberPermissions:Number},setup(b){const n=b,{_:j,debounce:S,pickBy:I}=T,t=B({params:{search:n.filters.search,field:n.filters.field,order:n.filters.order,perPage:n.perPage},selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,user:null,ArchivoNombre:"",dataSet:P().props.app.perpage}),c=a=>{t.params.field=a,console.log("🧈 debu data.params.field:",t.params.field),t.params.order=t.params.order==="asc"?"desc":"asc",console.log("🧈 debu data.params.order:",t.params.order)};E(()=>j.cloneDeep(t.params),S(()=>{let a=I(t.params);A.get(route("user.index"),a,{replace:!0,preserveState:!0,preserveScroll:!0})},150));const V=a=>{var s;a.target.checked===!1?t.selectedId=[]:(s=n.users)==null||s.data.forEach(f=>{t.selectedId.push(f.id)})},N=()=>{var a;((a=n.users)==null?void 0:a.data.length)==t.selectedId.length?t.multipleSelect=!0:t.multipleSelect=!1},q=D({archivo1:""});U(()=>{var a;t.ArchivoNombre=(a=q.archivo1)==null?void 0:a.name});const x=[{order:"name",label:"Nombre",type:"text",required:!0},{order:"email",label:"Email",type:"text",required:!0},{order:"identificacion",label:"Identificacion",type:"text",required:!0},{order:"area",label:"area",type:"text",required:!0},{order:"cargo",label:"cargo",type:"text",required:!0},{order:"sexo",label:"sexo",type:"foreign",nameid:"sexo_S",required:!1},{order:"celular",label:"celular",type:"text",required:!1},{order:"fecha_nacimiento",label:"Fecha nacimiento",type:"date",required:!1}];return(a,s)=>{const f=F("tooltip");return d(),g(k,null,[l(i(K),{title:n.title},null,8,["title"]),l(Z,null,{default:y(()=>[l(G,{title:b.title,breadcrumbs:b.breadcrumbs},null,8,["title","breadcrumbs"]),e("div",le,[e("div",oe,[e("div",ne,[m(l(J,{class:"rounded-none",onClick:s[0]||(s[0]=r=>t.createOpen=!0)},{default:y(()=>[$(o(a.lang().button.add),1)]),_:1},512),[[h,a.can(["create user"])]]),a.can(["create user"])?(d(),u(Y,{key:0,show:t.createOpen,onClose:s[1]||(s[1]=r=>t.createOpen=!1),roles:n.roles,title:n.title,titulos:x},null,8,["show","roles","title"])):w("",!0),a.can(["update user"])?(d(),u(ee,{key:1,show:t.editOpen,onClose:s[2]||(s[2]=r=>t.editOpen=!1),user:t.user,roles:n.roles,title:n.title,titulos:x},null,8,["show","user","roles","title"])):w("",!0),l(te,{show:t.deleteOpen,onClose:s[3]||(s[3]=r=>t.deleteOpen=!1),user:t.user,title:n.title},null,8,["show","user","title"]),l(se,{show:t.deleteBulkOpen,onClose:s[4]||(s[4]=r=>(t.deleteBulkOpen=!1,t.multipleSelect=!1,t.selectedId=[])),selectedId:t.selectedId,title:n.title},null,8,["show","selectedId","title"])])]),e("div",ie,[e("div",de,[e("div",ce,[l(W,{modelValue:t.params.perPage,"onUpdate:modelValue":s[5]||(s[5]=r=>t.params.perPage=r),dataSet:t.dataSet},null,8,["modelValue","dataSet"]),m((d(),u(C,{onClick:s[6]||(s[6]=r=>t.deleteBulkOpen=!0),class:"px-3 py-1.5"},{default:y(()=>[l(i(O),{class:"w-5 h-5"})]),_:1})),[[h,t.selectedId.length!=0&&a.can(["delete user"])],[f,a.lang().tooltip.delete_selected]])]),n.numberPermissions>1?(d(),u(H,{key:0,modelValue:t.params.search,"onUpdate:modelValue":s[7]||(s[7]=r=>t.params.search=r),type:"text",class:"block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg",placeholder:"Nombre, correo o identificación"},null,8,["modelValue"])):w("",!0)]),e("div",pe,[e("table",me,[e("thead",ue,[e("tr",fe,[e("th",ye,[l(re,{checked:t.multipleSelect,"onUpdate:checked":s[8]||(s[8]=r=>t.multipleSelect=r),onChange:V},null,8,["checked"])]),he,e("th",{class:"px-2 py-4 cursor-pointer",onClick:s[9]||(s[9]=r=>c("name"))},[e("div",be,[e("span",null,o(a.lang().label.name),1),l(i(p),{class:"w-4 h-4"})])]),e("th",_e,o(a.lang().label.role),1),e("th",{class:"px-2 py-4 cursor-pointer",onClick:s[10]||(s[10]=r=>c("identificacion"))},[e("div",ge,[e("span",null,o(a.lang().label.identificacion),1),l(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:s[11]||(s[11]=r=>c("sexo"))},[e("div",we,[e("span",null,o(a.lang().label.sexo),1),l(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:s[12]||(s[12]=r=>c("fecha_nacimiento"))},[e("div",xe,[e("span",null,o(a.lang().label.edad),1),l(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:s[13]||(s[13]=r=>c("celular"))},[e("div",ve,[e("span",null,o(a.lang().label.celular),1),l(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:s[14]||(s[14]=r=>c("area"))},[e("div",ke,[e("span",null,o(a.lang().label.area),1),l(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:s[15]||(s[15]=r=>c("cargo"))},[e("div",$e,[e("span",null,o(a.lang().label.cargo),1),l(i(p),{class:"w-4 h-4"})])]),Ce])]),e("tbody",null,[(d(!0),g(k,null,L(b.users.data,(r,v)=>(d(),g("tr",{key:v,class:"border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"},[e("td",Oe,[m(e("input",{class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary",type:"checkbox",onChange:N,value:r.id,"onUpdate:modelValue":s[16]||(s[16]=_=>t.selectedId=_)},null,40,je),[[M,t.selectedId]])]),e("td",Se,o(++v),1),e("td",Ie,[e("span",Ve,[$(o(r.name)+" ",1),m(l(i(z),{class:"ml-[2px] w-4 h-4 text-primary dark:text-white"},null,512),[[h,r.email_verified_at]])]),e("span",Ne,o(r.email),1)]),e("td",qe,o(r.roles.length==0?"not selected":r.roles[0].name),1),e("td",Be,o(r.identificacion),1),e("td",Pe,o(r.sexo),1),e("td",Ee,o(i(ae)(r.fecha_nacimiento)),1),e("td",Ae,o(r.celular),1),e("td",De,o(r.area),1),e("td",Ue,o(r.cargo),1),e("td",Fe,[e("div",Te,[e("div",Ke,[m((d(),u(Q,{type:"button",onClick:_=>(t.editOpen=!0,t.user=r),class:"px-2 py-1.5 rounded-none"},{default:y(()=>[l(i(R),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[h,a.can(["update user"])],[f,a.lang().tooltip.edit]]),m((d(),u(C,{type:"button",onClick:_=>(t.deleteOpen=!0,t.user=r),class:"px-2 py-1.5 rounded-none"},{default:y(()=>[l(i(O),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[h,a.can(["delete user"])],[f,a.lang().tooltip.delete]])])])])]))),128))])])]),e("div",Le,[l(X,{links:n.users,filters:t.params},null,8,["links","filters"])])])])]),_:1})],64)}}};export{mt as default};
