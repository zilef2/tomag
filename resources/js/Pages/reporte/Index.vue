<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';

import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage, Link, useForm } from '@inertiajs/vue3';

import Pagination from '@/Components/Pagination.vue';
import { ChevronUpDownIcon, CheckCircleIcon, PencilIcon, TrashIcon,ArrowTrendingDownIcon,ArrowLongUpIcon } from '@heroicons/vue/24/solid';

import Create from '@/Pages/reporte/Create.vue';
import Edit from '@/Pages/reporte/Edit.vue';
import TerminarReporte from '@/Pages/reporte/TerminarReporte.vue';
import Delete from '@/Pages/reporte/Delete.vue';
import DeleteBulk from '@/Pages/reporte/DeleteBulk.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import ClockWorking from '@/Components/uiverse/ClockWorking.vue';

import { TimeTo12Format, formatDate, CalcularAvg, number_format } from '@/global.ts';
import InputError from "@/Components/InputError.vue";
import vSelect from "vue-select"; import "vue-select/dist/vue-select.css";
import '@vuepic/vue-datepicker/dist/main.css';


const { _, debounce, pickBy } = pkg
const props = defineProps({
    fromController: Object,
    total: Number,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    title: String,

    numberPermissions: Number,
    losSelect: Object,
    Trabajadores: Object,
})

const data = reactive({
    params: {
        search: props.filters.searchDate,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
        searchdia: props.filters.searchdia,
        soloTiEstimado: props.filters.soloTiEstimado,
        ultimosmeses: props.filters.ultimosmeses,
        FiltroCentro: props.filters.FiltroCentro,
    },
    generico: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    TerminarOpen: false,
    deleteOpen: false,
    dataSet: usePage().props.app.perpage,
    totalEmpleados: 0,

    deleteBulkOpen: false,
    //v-sticky
    hayCongelado:0,
})

// onMounted(()=>{
//     if(props.filters.ultimosmeses == null)
//         data.param.ultimosmeses = true
// })
const order = (field,CanOrder) => {
    if(CanOrder){
        data.params.field = field
        data.params.order = data.params.order === "asc" ? "desc" : "asc"
    }
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("reporte.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 350))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((reporte) => {
            data.selectedId.push(reporte.id)
        })
    }
}
const select = () => {
    data.multipleSelect = props.reportes?.data.length === data.selectedId.length;
}
// const form = useForm({ })
watchEffect(() => {})

// text // number // dinero // date // datetime // foreign // decimal
const titulos = [
    { order: 'fecha', label: 'fecha', type: 'date' ,CanOrder: true},
    { order: 'operario_id', label: 'operario', type: 'foreign', nameid: 'operario_s' ,CanOrder: true},
    { order: 'hora_inicial', label: 'hora inicial', type: 'time' ,CanOrder: true},
    { order: 'hora_final', label: 'hora final', type: 'time' ,CanOrder: true},
    { order: 'tiempo_transcurrido', label: 'Tiempo Transcurrido', type: 'decimal', nameid: 'tiempo_transcurrido' ,CanOrder: true},
    { order: 'ordentrabajo_id', label: 'ordentrabajo', type: 'foreign', nameid: 'ordentrabajo_s' ,CanOrder: true},
    { order: 'centrotrabajo_id', label: 'centrotrabajo', type: 'foreign', nameid: 'centrotrabajo_s' ,CanOrder: true},
    { order: 'actividad_id', label: 'actividad', type: 'foreign', nameid: 'actividad_s' ,CanOrder: true},
    // { order: 'OTItem', label: 'ordentrabajo', type: 'text' ,CanOrder: false},
    // { order: 'TiempoEstimado', label: 'TiempoEstimado', type: 'text' ,CanOrder: true},
    { order: 'disponibilidad_id', label: 'disponibilidad', type: 'foreign', nameid: 'disponibilidad_s' ,CanOrder: true},
    { order: 'reproceso_id', label: 'reproceso', type: 'foreign', nameid: 'reproceso_s' ,CanOrder: true},
];

</script>

<template>
<!--    <Head :title="props.title" />-->

    <AuthenticatedLayout>
<!--        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>-->
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-md mt-4" @click="data.createOpen = true" v-if="can(['create reporte'])">
                        {{ lang().button.add }} {{ props.title }}
                    </PrimaryButton>
                    <Create v-if="can(['create reporte'])" :numberPermissions="props.numberPermissions"
                        :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        :losSelect=props.losSelect
                        :Trabajadores=props.Trabajadores
                    />

                    <Edit v-if="can(['update reporte']) && numberPermissions > 1" :numberPermissions="props.numberPermissions"
                        :show="data.editOpen"
                        @close="data.editOpen = false" :generica="data.generico" :title="props.title"
                        :losSelect=props.losSelect
                        :Trabajadores=props.Trabajadores
                    />

                    <TerminarReporte v-if="can(['read reporte'])" :numberPermissions="props.numberPermissions" :show="data.TerminarOpen"
                        @close="data.TerminarOpen = false" :generica="data.generico" :title="props.title"
                        />

                    <Delete v-if="can(['delete reporte'])" :numberPermissions="props.numberPermissions"
                        :show="data.deleteOpen" @close="data.deleteOpen = false" :generica="data.generico"
                        :title="props.title" />

                    <DeleteBulk :show="data.deleteBulkOpen"
                        @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                        :selectedId="data.selectedId" :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg ">
                <div class=" flex justify-between px-0 my-1 h-22 overflow-x-auto overflow-y-hidden pb-2">
                    <div class="inline-flex gap-2">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" class="ml-2 mt-4 h-12" />
                        <DangerButton @click="data.deleteBulkOpen = true"
                                      v-show="data.selectedId.length !== 0 && can(['delete reporte'])" class="px-3 py-1.5 h-10 my-auto"
                                      v-tooltip="lang().tooltip.delete_selected">
                          <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                        <PrimaryButton @click="data.hayCongelado = data.selectedId[0]"
                                       v-show="data.selectedId.length !== 0 && can(['delete reporte'])" class="px-3 py-1.5 h-10 my-auto"
                                       v-tooltip="'Congelar'">
                          <ArrowLongUpIcon class="w-5 h-5" />
                        </PrimaryButton>
                        <div v-if="numberPermissions > 1" class="mx-1 mt-4 w-40 flex gap-4">
                        <div class="my-1">
                          <label for="soloval" class="text-sm mx-2 whitespace-nowrap">Tiempo Estimado</label>
                          <input v-if="filters !== null" v-model="data.params.soloTiEstimado"
                                 id="soloval" type="checkbox"
                                 class="bg-gray-200 h-5 w-5 ml-[12px] " />
                          <div class="inline-flex my-1">
                            <label for="soloval" class="text-sm mx-2 whitespace-nowrap">Mostrar todos</label>
                            <input v-if="filters !== null" v-model="data.params.ultimosmeses"
                                   id="soloval" type="checkbox"
                                   class="bg-gray-200 h-5 w-5 ml-[34px]" />
                          </div>
                        </div>
                      </div>

                    <!--CT-->
                    <div v-if="numberPermissions > 1" class="mt-2 sm:relative md:fixed z-30 md:left-1/3 lg:left-1/2">
                      <label name="centrotrabajo_id" class="mx-2 mt-2 text-sm dark:text-white"> Centro de trabajo </label>
                      <v-select :options="props.losSelect.centrotrabajo" label="title" class="dark:bg-gray-400 w-64 mt-1 z-20"
                                v-model="data.params.FiltroCentro"></v-select>
                    </div>
                    </div>
                    <!--searches -->
                    <div v-if="numberPermissions > 1" class="mt-4 sm:flex-wrap lg:flex-inline gap-4 right-0">
                      <TextInput v-if="props.numberPermissions > 1" v-model="data.params.searchdia" type="number" max="31" min="0"
                          class="mx-1 xl:mr-2 w-32 rounded-lg sm:h-6 lg:h-12 mt-1" placeholder="Buscar día"/>
                      <TextInput v-if="props.numberPermissions > 1" v-model="data.params.searchDate" type="date"
                          class="mx-1 xl:mr-2 w-32 rounded-lg sm:h-6 lg:h-12 mt-1" placeholder="Buscar por Fecha (mes o año) " />
                    </div>
                </div>
                <div class="max-h-[630px] overflow-y-scroll">
                    <table v-if="props.total > 0" class=" w-full">
                        <thead class="-mt-1 top-0 bg-gray-200 sticky z-[5] capitalize text-sm dark:border-gray-700 dark:bg-black" >
                            <tr class="dark:bg-gray-900/50 text-left">
                                <th v-if="props.numberPermissions > 1" class="px-2 py-4 text-center dark:bg-black">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th class="px-2 py-4">Accion</th>

                                <th class="px-2 py-4 text-center">#</th>
                                <th v-for="titulo in titulos" class="px-2 py-4 cursor-pointer min-w-min"
                                    v-on:click="order(titulo['order'], titulo['CanOrder'])">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label[titulo['label']] }}</span>
                                        <ChevronUpDownIcon v-if="titulo['CanOrder']" class="w-4 h-4" />
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <!-- {{ props.fromController.data[0] }} -->
                        <tbody>
                            <div v-if="data.hayCongelado !== 0" v-sticky="{ zIndex: 100 }"
                                 class="w-full bg-white border border-blue-500 inamovible">
                                <tr class="dark:bg-gray-900/50 text-left">
                                    <th class="px-2 py-4"> </th>
                                    <th class="px-2 py-4"> </th>

                                    <th class="px-2 py-4 text-center">
                                        <PrimaryButton @click="data.hayCongelado = 0"
                                            v-show="data.hayCongelado && can(['delete reporte'])"
                                            class="px-3 py-1.5"
                                            v-tooltip="'Descongelar'">
                                        <ArrowTrendingDownIcon class="w-5 h-5" />
                                    </PrimaryButton>
                                    </th>
                                    <th v-for="titulo in titulos" class="px-2 py-4 cursor-pointer">
                                        <div class="flex justify-between items-center">
                                            <p class="w-20 text-sm">{{ lang().label[titulo['label']] }}</p>
                                        </div>
                                    </th>
                                </tr>
                                <tr >
                                    <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">  </td>
                                    <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">  </td>
                                    <td class="whitespace-nowrap py-4 px-2 sm:py-3">    </td>
                                    <td v-for="titulo in titulos" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                        <span v-if="titulo['type'] === 'text'"> {{ props.fromController.data[0][titulo['order']] }} </span>
                                        <span v-if="titulo['type'] === 'time'"> {{ TimeTo12Format(props.fromController.data[0][titulo['order']]) }} </span>
                                        <span v-if="titulo['type'] === 'number'"> {{ number_format(props.fromController.data[0][titulo['order']], 0, false) }} </span>
                                        <span v-if="titulo['type'] === 'decimal'"> {{ number_format(props.fromController.data[0][titulo['order']], 1, false) }} </span>
                                        <span v-if="titulo['type'] === 'dinero'"> {{ number_format(props.fromController.data[0][titulo['order']], 0, true) }} </span>
                                        <span v-if="titulo['type'] === 'date'"> {{ formatDate(props.fromController.data[0][titulo['order']], '') }} </span>
                                        <span v-if="titulo['type'] === 'datetime'"> {{ formatDate(props.fromController.data[0][titulo['order']], 'conLaHora') }} </span>
                                        <span v-if="titulo['type'] === 'foreign'"> {{ props.fromController.data[0][titulo['nameid']] }} </span>
                                        <span v-if="titulo['order'] === 'hora_final' && props.fromController.data[0][titulo['order']] == null">-</span>
                                    </td>
                                </tr>
                            </div>
                            <tr v-for="(clasegenerica, indexu) in props.fromController.data" :key="indexu"
                                class="hover:dark:bg-gray-900/20">
                                <td v-if="props.numberPermissions > 1" class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <input
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"
                                        type="checkbox" @change="select" :value="clasegenerica.id"
                                        v-model="data.selectedId" />
                                </td>
                                <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3">
                                    <div class="flex justify-center items-center">
                                        <div class="rounded-md overflow-hidden">
                                            <InfoButton v-show="can(['update reporte'])" type="button"
                                                @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <InfoButton v-if="!clasegenerica.hora_final" v-show="can(['create reporte'])" type="button"
                                                @click="(data.TerminarOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.finalizarTarea">
                                                <CheckCircleIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton v-show="can(['delete reporte'])" type="button"
                                                @click="(data.deleteOpen = true), (data.generico = clasegenerica)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>
                                <td v-for="titulo in titulos" class="whitespace-wrap py-4 px-2 sm:py-3">
                                    <span v-if="titulo['type'] === 'text'"> {{ clasegenerica[titulo['order']] }} </span>
                                    <!-- <span v-if="titulo['type'] === 'time'"> {{ (clasegenerica[titulo['order']]).slice(0,-3) }} </span> -->
                                    <span v-if="titulo['type'] === 'time'"> {{ TimeTo12Format(clasegenerica[titulo['order']]) }} </span>
                                    <span v-if="titulo['type'] === 'number'"> {{ number_format(clasegenerica[titulo['order']], 0, false) }} </span>
                                    <span v-if="titulo['type'] === 'decimal'"> {{ number_format(clasegenerica[titulo['order']], 1, false) }} </span>
                                    <span v-if="titulo['type'] === 'dinero'"> {{ number_format(clasegenerica[titulo['order']], 0, true) }} </span>
                                    <span v-if="titulo['type'] === 'date'"> {{ formatDate(clasegenerica[titulo['order']], '') }} </span>
                                    <span v-if="titulo['type'] === 'datetime'"> {{ formatDate(clasegenerica[titulo['order']], 'conLaHora') }} </span>
                                    <span v-if="titulo['type'] === 'foreign'"> {{ clasegenerica[titulo['nameid']] }} </span>
                                    <span v-if="titulo['order'] === 'hora_final' && clasegenerica[titulo['order']] == null">
                                        <ClockWorking />
                                    </span>
                                </td>
                            </tr>

                            <!-- totales -->
                            <!-- <tr>
                                <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> </td>
                                <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> </td>
                                <td v-if="numberPermissions > 1" class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 font-extrabold text-center"> Hora inicial Promedio: </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    {{ CalcularAvg(props.fromController.data, 'hora_inicial', true) }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"> </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 font-extrabold text-center"> Tiempo Estimado Promedio: </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    {{ CalcularAvg(props.fromController.data, 'TiempoEstimado') }}
                                </td>
                            </tr> -->
                        </tbody>
                    </table>
                    <h2 v-else class="text-center text-xl my-8">Sin Registros</h2>
                </div>
                <div v-if="props.total > 0"
                    class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
    <style scope>
        .inamovible {
            position: fixed;
            top: 0;
            left: 250px;
            z-index: 5;
        }
        /* Añade estilos adicionales según tus necesidades */
    </style>
