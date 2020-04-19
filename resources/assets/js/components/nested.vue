<template>
    <draggable
        v-bind="dragOptions"
        tag="div"
        class="item-container"
        :value="value"
        :list="list"
        @input="emitter"
    >
        <div class="item-group" :key="key(el)" v-for="(el, index) in realValue">
            <div class="item clearfix">
                <div class="float-left">
                    {{ el.name }}
                </div>
                <div class="float-right">
                    <button type="button" aria-label="Close" class="close" @click="remove(index)">×</button>

                    <b-button class="close mx-1" v-b-modal="key(el)">
                        <i class="mdi mdi-pencil"></i>
                    </b-button>
                </div>
            </div>
            <modal :id="key(el)" :item="el" @save="save(index, $event)"></modal>
            <nested class="item-sub" :list="el.elements" />
        </div>
    </draggable>
</template>

<script>
    export default {
        name: "nested",
        props: {
            value: {
                required: false,
                type: Array,
                default: null
            },
            list: {
                required: false,
                type: Array,
                default: null
            }
        },

        data() {
            return {
                name: '',
                url: ''
            };
        },

        computed: {

            realValue() {
                return this.value ? this.value : this.list;
            },

            dragOptions() {
                return {
                    animation: 0,
                    group: "description",
                    disabled: false,
                    ghostClass: "ghost"
                };
            }
        },
        methods: {

            remove(index) {
                this.realValue.splice(index, 1);
                this.$emit('input', this.realValue);
            },

            save(index, $event) {
                let element = this.realValue[index];

                this.$bvModal.hide(this.key(element));

                element.name          = $event.name;
                element.url           = $event.url;
                this.realValue[index] = element;

                this.$emit("input", this.realValue);
            },

            key(el) {
                return el.id + el.name + el.url;
            },

            emitter(value) {
                this.$emit("input", value);
            }
        }
    };
</script>
