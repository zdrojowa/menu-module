<template>
    <div>
        <div class="card">
            <div class="card-header clearfix">
                <h4 class="card-title float-left"><i class="mdi mdi-menu"></i> Struktura</h4>
                <div class="float-right">
                    <b-button variant="success" v-b-modal="'0'"><i class="mdi mdi-plus"></i> Dodaj</b-button>
                </div>
            </div>

            <div class="card-body">
                <nested v-model="elements"></nested>
            </div>
        </div>

        <modal id='0' :item="element" @save="add"></modal>
    </div>
</template>

<script>
    export default {
        name: "structure",
        props: {
            value: {
                required: false,
                type: Array,
                default: []
            }
        },

        data() {
            return {
                element: {id: 0, name: '', url: '', elements: []},
                elements: []
            };
        },

        created() {
            this.elements = this.value;
        },

        methods: {
            add($event) {
                this.elements.push({id: 0, name: $event.name, url: $event.url, elements: []});
                this.$bvModal.hide('0');
            }
        },

        watch: {
            value() {
                this.elements = this.value;
            },

            elements(){
                this.$emit("input", this.elements);
            }
        }
    };
</script>
