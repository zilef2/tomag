<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted, reactive, watch, watchEffect } from 'vue';
import vSelect from "vue-select"; import "vue-select/dist/vue-select.css";
import VueDatePicker from '@vuepic/vue-datepicker';

import { LookForValueInArray, TransformTdate, formatTime } from '@/global.ts';

const props = defineProps({
    show: Boolean,
    title: String,
    generica: Object,
    losSelect: Object,
    numberPermissions: Number,
    Trabajadores: Object,
})

const emit = defineEmits(["close"]);

const opcinesActividadOTros = [{ title: 'Actividad', value: 0 }, { title: 'Reproceso', value: 1 }, { title: 'Disponibilidad(paro)', value: 2 }];
const arrayMostrarDelCodigo = ['Nombre Tablero', '% avance', 'OT+Item', 'Tiempo estimado'];

const data = reactive({
    params: {
        pregunta: ''
    },
    actividad_id: props.losSelect.actividad,
    centrotrabajo_id: props.losSelect.centrotrabajo,
    disponibilidad_id: props.losSelect.disponibilidad,
    ordentrabajoSelect: props.losSelect.ordentrabajo,
    reproceso_id: props.losSelect.reproceso,
    valorInactivo: 'NA',
    Select_ordentrabajo: [],
    mensajeFalta: '',
    BanderaTipo: true,
})


//very usefull
const justNames = [
    // 'codigo',
    'tipoReporte',
    'fecha',
    'hora_inicial',
    'hora_final',
    'actividad_id',
    'centrotrabajo_id',
    'disponibilidad_id',
    'operario_id',
    'ordentrabajo_id',
    'reproceso_id',

    'Select_ordentrabajo',
    'otitem',
    'user_id',

]; const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])) });

onMounted(() => {});

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    } else {
        data.BanderaTipo = true
    }
})

let ValidarNotNull = (campos) => {
    let sonObligatorios = '';
    try {
        campos.forEach((value, i) => {
            if (typeof form[value] === 'undefined' || form[value] === null || form[value].length === 0) { //&& form[value] != ''
                sonObligatorios = value
                throw new Error('BreakException');
            }
        })
    } catch (e) {
        // if (e.message !== 'BreakException') throw e;
    }
    return sonObligatorios;
}

let ValidarCreateReporte = () => {
    let tipo = form.tipoReporte.value;
    let result = true;
    const mensaje = ' es obligatorio'
    if (tipo === 0) {
        result = ValidarNotNull([
            'Select_ordentrabajo',
            'centrotrabajo_id',
            'actividad_id',
        ])
    } //acti

    if (tipo === 1) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'Select_ordentrabajo',
            'actividad_id',
            'reproceso_id',
        ])
    } //reproceso

    if (tipo === 2) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'disponibilidad_id',
        ])
    } //disponibilidad

    let objectMessages = {
        'Select_ordentrabajo': 'Orden trabajo',
        'actividad_id': 'Actividad',
        'reproceso_id': 'Reproceso',
        'centrotrabajo_id': 'Centro de trabajo',
        'disponibilidad_id': 'Disponibilidad',
    }
    if (result != '') return objectMessages[result] + mensaje
    else return result
}

const update = () => {
    form.ordentrabajo_id = form.Select_ordentrabajo
    // data.mensajeFalta = ValidarCreateReporte();
    data.mensajeFalta = ValidarCreateReporte();

    let StringResultAny;
    StringResultAny = LookForValueInArray(props.losSelect.centrotrabajo, form.centrotrabajo_id)
    form.centrotrabajo_id = StringResultAny !== '' ? StringResultAny : '';

    if(form.tipoReporte.value === 0){
        StringResultAny = LookForValueInArray(props.losSelect.actividad, form.actividad_id)
        form.actividad_id = StringResultAny !== '' ? StringResultAny : '';
    }
    if(form.tipoReporte.value === 1){
        StringResultAny = LookForValueInArray(props.losSelect.actividad, form.actividad_id)
        form.actividad_id = StringResultAny !== '' ? StringResultAny : '';

        StringResultAny = LookForValueInArray(props.losSelect.reproceso, form.reproceso_id)
        form.reproceso_id = StringResultAny !== '' ? StringResultAny : '';
    }

    if(form.tipoReporte.value === 2){ //disponibilidad
        StringResultAny = LookForValueInArray(props.losSelect.disponibilidad, form.disponibilidad_id)
        form.disponibilidad_id = StringResultAny !== '' ? StringResultAny : '';
    }


    if(data.mensajeFalta === ''){
        form.put(route('reporte.update', props.generica?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => null,
        })
    }
}

watch(() => form.Select_ordentrabajo, (newX) => {})

watch(() => props.show, () => {

    if (data.BanderaTipo) {

        data.BanderaTipo = false
        form.tipoReporte = opcinesActividadOTros[props.generica?.tipoReporte]

        let posicionOrden = props.losSelect.ordentrabajo.findIndex(obj =>{

            if(obj.value === parseInt(props.generica?.ordentrabajo_id)){

              // props.generica?.ordentrabajo_id
            console.log(obj.value , props.generica?.ordentrabajo_id)
              return obj
            }
        }
        )

        form.Select_ordentrabajo = data.ordentrabajoSelect[posicionOrden]
        form.centrotrabajo_id = data.centrotrabajo_id[props.generica?.centrotrabajo_id]

      console.log(props.losSelect.ordentrabajo)
      console.log(props.generica?.ordentrabajo_id)
      console.log(posicionOrden)
        form.actividad_id = data.actividad_id[props.generica?.actividad_id] ? data.actividad_id[props.generica?.actividad_id] : null
        form.disponibilidad_id = data.disponibilidad_id[props.generica?.disponibilidad_id] ? data.disponibilidad_id[props.generica?.disponibilidad_id] : null
        form.reproceso_id = data.reproceso_id[props.generica?.reproceso_id] ? data.reproceso_id[props.generica?.reproceso_id] : null

        form.cantidad = props.generica?.cantidad
        form.fecha = props.generica?.fecha
        form.hora_inicial = props.generica?.hora_inicial


        let posicionUser = props.Trabajadores.findIndex(obj => obj.value === props.generica?.operario_id)
        form.user_id = props.Trabajadores[posicionUser]


    } else {
        data.BanderaTipo = !props.show
    }
})

watch(() => form.centrotrabajo_id, (newCentro,old) => {
    if(newCentro && typeof newCentro.value !== 'undefined'){
        let actividadesDelCentro = 'centrotrabajo'+newCentro.title
        data.actividad_id = props.losSelect[actividadesDelCentro]

        if(newCentro.value !== props.generica.centrotrabajo_id){
            form.actividad_id = { title: 'Seleccione actividad', value: null }
        }
    }
})

</script>


<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close'), data.BanderaTipo = true">
            <form class="px-6 my-8" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                    <div v-if="props.numberPermissions > 9" id="opcinesActividadO"  class="xl:col-span-2 col-span-1">
                        <label name=""> Reportar en nombre de: </label>
                        <v-select :options="props.Trabajadores" disabled label="title" v-model="form.user_id"></v-select>
                    </div>
                    <div id="opcinesActividadO" class="xl:col-span-2 col-span-1">
                        <label name=""> Tipo de reporte </label>
                        <v-select :options="opcinesActividadOTros" label="title" disabled
                            v-model="form.tipoReporte"></v-select>
                    </div>

                    <!-- empieza -->
                    <div class="xl:col-span-1 col-span-1">
                        <InputLabel for="fecha" :value="lang().label['fecha']" />
                        <TextInput id="fecha" type="date" class="mt-1 block w-full bg-gray-200" v-model="form['fecha']"
                            disabled placeholder="fecha" :error="form.errors['fecha']" />
                        <InputError class="mt-2" :message="form.errors['fecha']" />
                    </div>
                    <div class=" col-span-1">
                        <InputLabel for="hora_inicial" :value="lang().label['hora inicial']" />
                        <TextInput id="hora_inicial" type="time" class="mt-1 block w-full bg-gray-200"
                            v-model="form['hora_inicial']" placeholder="hora_inicial"
                            :error="form.errors['hora_inicial']" step="60" />
                        <InputError class="mt-2" :message="form.errors['hora_inicial']" />
                    </div>

                    <div id="Sordentrabajo" v-if="form.tipoReporte.value !== 2" class="lg:col-span-2 col-span-1">
                        <label name="Select_ordentrabajo"> Orden de trabajo </label>
                        <v-select :options="losSelect.ordentrabajo" label="title" class="dark:bg-gray-400"
                                  v-model="form.Select_ordentrabajo"
                        ></v-select>
                        <InputError class="mt-2" :message="form.errors['ordentrabajo_id']" />
                    </div>


                    <div id="Scentrotrabajo" class=" col-span-1">
                        <label name="centrotrabajo_id"> Centro de trabajo </label>
                        <v-select :options="data['centrotrabajo_id']" label="title"
                            v-model="form['centrotrabajo_id']"></v-select>
                        <InputError class="mt-2" :message="form.errors['centrotrabajo_id']" />
                    </div>



                    <!-- eleccion -->
                    <div id="Sactividad" v-if="form.tipoReporte.value === 0 || form.tipoReporte.value === 1"
                        class="xl:col-span-2 col-span-1">
                        <label name="label_actividad_id"> Actividad </label>
                        <v-select :options="data['actividad_id']" label="title" required
                            v-model="form.actividad_id"></v-select>
                        <InputError class="mt-2" :message="form.errors['actividad_id']" />
                    </div>
                    <div id="Sreproceso" v-if="form.tipoReporte.value === 1" class="xl:col-span-2 col-span-1">
                        <label name="reproceso_id"> Reproceso</label>
                        <v-select :options="data['reproceso_id']" label="title" required
                            v-model="form['reproceso_id']"></v-select>
                        <InputError class="mt-2" :message="form.errors['reproceso_id']" />
                    </div>
                    <div id="Sdisponibilidad" v-if="form.tipoReporte.value === 2" class="xl:col-span-3  col-span-1">
                        <label name="disponibilidad_id"> Disponibilidad</label>
                        <v-select :options="data['disponibilidad_id']" label="title" required
                            v-model="form['disponibilidad_id']"></v-select>
                        <InputError class="mt-2" :message="form.errors['disponibilidad_id']" />
                    </div>
                    <!-- termina -->
                </div>


                <div class=" mb-8 mt-[360px] flex justify-end">
                    <h2 v-if="data.mensajeFalta != ''"
                        class="mx-12 px-8 text-lg font-medium text-red-600 bg-red-50 dark:text-gray-100">
                        {{ data.mensajeFalta }}
                    </h2>

                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>

<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}

[name="labelSelectVue"],
.muted {
    color: #1b416699;
}

[name="labelSelectVue"] {
    /* font-size: 22px; */
    font-weight: 600;
}
</style>
