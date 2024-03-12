<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onBeforeUnmount, onMounted, reactive, watch, watchEffect} from 'vue';
import vSelect from "vue-select"; import "vue-select/dist/vue-select.css"; import '@vuepic/vue-datepicker/dist/main.css';
import {DiferenciaMinutos, formatTime, TransformTdate} from '@/global.ts';



const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,

    losSelect: Object,
    numberPermissions: Number,
    Trabajadores: Object,
})
const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
    actividad_id:props.losSelect.actividad,
    centrotrabajo_id:props.losSelect.centrotrabajo,
    disponibilidad_id:props.losSelect.disponibilidad,
    ordentrabajo_id:props.losSelect.ordentrabajo,
    reproceso_id:props.losSelect.reproceso,
    valorInactivo:'NA',
    Select_ordentrabajo: [],
    mensajeFalta: '',
    BanderaTipo:true,
    limiteMinimo:'',
    tempCentro:0
})

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

const CalcularHoraActual = ()=>{
  let horaActual = new Date();
  horaActual.setHours(horaActual.getHours() - 2);
  let formatoHora = (valor) => (valor < 10 ? `0${valor}` : valor);
  return `${formatoHora(horaActual.getHours())}:${formatoHora(horaActual.getMinutes())}`;
}
onMounted(() => {
    data.limiteMinimo = CalcularHoraActual()
    setInterval(() => {
        data.limiteMinimo = CalcularHoraActual()
    }, 60000);
});

const disableContextMenu = (event) => {
  // Prevent the default context menu behavior
  event.preventDefault();
  return false;
};


let ValidarNotNull = (campos) =>{
    let sonObligatorios = '';
    try{
        campos.forEach((value,i) => {
            // console.log("🧈 debu form[value]:", form[value]);
            // console.log("🧈 debu form[value]:", form[value].value);
            if(typeof form[value] === 'undefined' || form[value] === null || form[value].value === null || form[value].length === 0){ //&& form[value] != ''
                sonObligatorios = value
                throw new Error('BreakException');
            }
        })
    } catch (e) {
        // if (e.message !== 'BreakException') throw e;
    }
    return sonObligatorios;
}
let ValidarCreateReporte = () =>{
    let tipo = form.tipoReporte.value;
    let result = true;
    const mensaje = '. Campo obligatorio'

    let horaactual = new Date().getHours()
    console.log(horaactual)
    console.log('espacio\n')
    console.log(form.hora_inicial)
    console.log('espacio2\n')
    let minutosDif = DiferenciaMinutos( horaactual+ ':00', form.hora_inicial)
    console.log(minutosDif)

    if(minutosDif > 0) return 'Ha pasado mucho tiempo!';

    if(tipo === 0){
        result = ValidarNotNull([
            'Select_ordentrabajo',
            'centrotrabajo_id',
            'actividad_id',
        ])
    } //acti

    if(tipo === 1) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'Select_ordentrabajo',
            'actividad_id',
            'reproceso_id',
        ])
    } //reproceso

    if(tipo === 2) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'disponibilidad_id',
        ])
    } //disponibilidad

    let objectMessages = {
        'Select_ordentrabajo':'Orden trabajo',
        'actividad_id':'Actividad',
        'reproceso_id':'Reproceso',
        'centrotrabajo_id':'Centro de trabajo',
        'disponibilidad_id':'Disponibilidad',
    }
    if(result !== '') return objectMessages[result] + mensaje
    else return result
}



// <!--<editor-fold desc="Watchers">-->

watchEffect(() => {
    if (props.show) {
        if(data.BanderaTipo){

            form.tipoReporte = { title: 'Actividad', value: 0 };
            data.BanderaTipo = false
        }

        form.errors = {}
        if(form.fecha === null || form.fecha === ''){
            let currentDate = new Date();
            form.fecha = (TransformTdate(currentDate,'')).substring(0,10);
            form.hora_inicial = formatTime()

        }

        //valores implicitos
        // console.clear();
    }else{
        data.BanderaTipo = true
    }
})



watch(() => form.tipoReporte, (newX) => {
    form.actividad_id = null
    form.centrotrabajo_id = null
    form.disponibilidad_id = null
    form.operario_id = null
    form.ordentrabajo_id = null
    form.reproceso_id = null
    form.Select_ordentrabajo = null
})

// watch(() => form.Select_ordentrabajo, (newX) => {})
//     form.actividad_id = { title: 'Seleccione actividad', value: null }
// <!--</editor-fold>-->


const create = () => {
    form.ordentrabajo_id = form.Select_ordentrabajo
    data.mensajeFalta = ValidarCreateReporte();
    form.hora_inicial = formatTime()
    if(data.mensajeFalta === ''){
        setTimeout(form.post(route('reporte.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => null,
        }), 500);
    }

}

const opcinesActividadOTros = [{ title: 'Actividad', value: 0 }, { title: 'Reproceso', value: 1 }, { title: 'Disponibilidad(paro)', value: 2 }];
const arrayMostrarDelCodigo = ['Nombre Tablero','% avance','OT+Item','Tiempo estimado'];
</script>

<template>
<!--    <meta http-equiv="refresh" content="120">-->

    <section class="space-y-6  dark:text-white">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'3xl'">
            <form class="px-6 my-8" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                    <div v-if="props.numberPermissions > 1" id="opcinesActividadO" class="xl:col-span-2 col-span-1">
                        <label class=" dark:text-white" name=""> Reportar en nombre de: <small>(Opcional) </small></label>
                        <v-select :options="props.Trabajadores" label="title" class="dark:bg-gray-400"
                        v-model="form.user_id"></v-select>
                    </div>
                    <div id="opcinesActividadO" class="xl:col-span-2 col-span-1 ">
                        <label class=" dark:text-white"> Tipo de reporte </label>
                        <v-select :options="opcinesActividadOTros" label="title" class="dark:bg-gray-400"
                        v-model="form.tipoReporte"></v-select>
                    </div>
                    <!-- empieza -->

                    <div class="xl:col-span-1 col-span-1">
                        <InputLabel for="fecha" :value="lang().label['fecha']" class=" dark:text-white"/>
                        <TextInput id="fecha" type="date" class="mt-1 block w-full bg-gray-200  dark:text-white"
                            v-model="form['fecha']" disabled placeholder="fecha"
                            :error="form.errors['fecha']" />
                        <InputError class="mt-2" :message="form.errors['fecha']" />
                    </div>
<!--                            :value="lang().label['hora inicial']" />-->
                    <div class=" dark:text-white col-span-1">
                        <InputLabel for="hora_inicial"
                            :value="lang().label['hora inicial'] + ', min: '+data.limiteMinimo" />
                        <TextInput id="hora_inicial" type="time" class="mt-1 block w-full"
                            v-model="form['hora_inicial']"  placeholder="hora_inicial"
                            :error="form.errors['hora_inicial']" step="60" />
                        <InputError class="mt-2" :message="form.errors['hora_inicial']" />
                    </div>

                    <div id="Sordentrabajo" v-if="form.tipoReporte.value !== 2" class="xl:col-span-2 col-span-1">
                        <label name="Select_ordentrabajo" class=" dark:text-white"> Orden de trabajo </label>
                        <v-select :options="losSelect.ordentrabajo" label="title" class="dark:bg-gray-400"
                            v-model="form.Select_ordentrabajo"
                        ></v-select>
                        <InputError class="mt-2" :message="form.errors['ordentrabajo_id']" />
                    </div>

<!--                    <div v-if="form.Select_ordentrabajo && form.tipoReporte.value !== 2" class="w-full lg:col-span-2 col-span-1  dark:text-white">-->
<!--                        <InputLabel :for="index" :value="arrayMostrarDelCodigo[0]" class=""/>-->
<!--                        <TextInput :id="index" type="text" disabled class="mt-1 block w-full bg-gray-200"/>-->
<!--                    </div>-->

<!--                    <div v-if="form.Select_ordentrabajo && form.tipoReporte.value !== 2" class="w-full col-span-1 dark:text-white">-->
<!--                        <InputLabel :for="index" :value="arrayMostrarDelCodigo[1]" />-->
<!--                        <TextInput :id="index" type="text" disabled class="mt-1 block w-full bg-gray-200"/>-->
<!--                    </div>-->

                    <div id="Scentrotrabajo" class=" col-span-1">
                        <label name="centrotrabajo_id" class=" dark:text-white"> Centro de trabajo </label>
                        <v-select :options="data['centrotrabajo_id']" label="title" class="dark:bg-gray-400"
                            v-model="form['centrotrabajo_id']"
                        ></v-select>
                        <InputError class="mt-2" :message="form.errors['centrotrabajo_id']" />
                    </div>



                    <!-- eleccion -->
                    <div id="actividad" v-if="form.tipoReporte.value === 0 || form.tipoReporte.value === 1" class="xl:col-span-2 col-span-1">
                        <label name="actividad_id" class=" dark:text-white"> Actividad </label>
                        <v-select :options="data['actividad_id']" label="title" required
                            v-model="form['actividad_id']" class="dark:bg-gray-400"
                        ></v-select>
                        <InputError class="mt-2" :message="form.errors['actividad_id']" />
                    </div>
                    <div id="reproceso" v-if="form.tipoReporte.value == 1" class="xl:col-span-2 col-span-1">
                        <label name="reproceso_id" class=" dark:text-white"> Reproceso</label>
                        <v-select :options="data['reproceso_id']" label="title" required
                            v-model="form['reproceso_id']" class="dark:bg-gray-400"
                        ></v-select>
                        <InputError class="mt-2" :message="form.errors['reproceso_id']" />
                    </div>
                    <div id="disponibilidad" v-if="form.tipoReporte.value == 2" class="xl:col-span-3  col-span-1">
                        <label name="disponibilidad_id" class=" dark:text-white"> Disponibilidad</label>
                        <v-select :options="data['disponibilidad_id']" label="title" required
                            v-model="form['disponibilidad_id']" class="dark:bg-gray-400"
                        ></v-select>
                        <InputError class="mt-2" :message="form.errors['disponibilidad_id']" />
                    </div>
                    <!-- termina -->
                </div>


                <div class=" mb-8 mt-[360px] flex justify-end">
                    <h2 v-if="data.mensajeFalta !== ''" class="mx-12 px-8 text-lg font-medium text-red-600 bg-red-50 dark:text-white dark:bg-gray-800">
                        {{ data.mensajeFalta }}
                    </h2>

                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
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
