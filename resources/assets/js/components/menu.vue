<template>
    <form method="POST" :action="url" @submit="validate" enctype="multipart/form-data">

        <slot></slot>
        <input v-if="_id" type="hidden" name="translation" :value="_id">

        <div class="card mb-2">

            <div class="card-header clearfix">
                <h4 v-if="_id" class="card-title float-left"><i class="mdi mdi-pencil"></i> Edytowanie menu</h4>
                <h4 v-else class="card-title float-left"><i class="mdi mdi-plus"></i> Dodawanie nowego menu</h4>
                <div class="float-right">
                    <button type="submit" class="btn btn-primary">Zapisz</button>
                </div>
            </div>

            <div class="card-body row">

                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Nazwa</label>
                        <input type="text" :class="getInputClass('name')" name="name" placeholder="Wpisz nazwę" v-model.lazy="obj.name">
                        <small v-if="hasError('name')" class="error mt-2 text-danger">{{ errors.name[0] }}</small>
                    </div>
                </div>

                <div class="col-lg-6">

                    <div class="form-group">
                        <label>Język</label>
                        <multiselect v-model.lazy="obj.lang" :options="langs" track-by="key" label="name" placeholder="Wybierz język"></multiselect>
                    </div>
                </div>
            </div>
        </div>

        <structure v-model="obj.structure"></structure>

    </form>
</template>

<script>

    export default {
        name: 'menu-editor',
        props : ['_id', 'lang'],

        data() {
            return {
                langs: [],
                mainLang: 'pl',
                types: [],
                obj: {
                    id: 0,
                    name: '',
                    structure: [],
                    lang: {key: 'pl', name: 'Polski'},
                    type: {id: 'link', name: 'LINK'}
                },
                errors: {
                    name: {}
                }
            };
        },

        created: function() {
            this.getLangs();
        },

        computed: {

            url() {
                return this.obj.id ? ('/dashboard/menu/' + this.obj.id) : '/dashboard/menu/store';
            }
        },

        methods: {

            hasError(key) {
                return this.errors[key].length > 0;
            },

            getInputClass(key) {
                let className = 'form-control ';
                if (this.hasError(key)) {
                    className += 'is-invalid';
                } else {
                    if (this.obj[key]) {
                        className += 'is-valid';
                    }
                }
                return className;
            },

            getTypes() {
                axios.get('/dashboard/menu/types')
                    .then(res => {
                        this.types = res.data;
                        this.getMenu();
                    }).catch(err => {
                    console.log(err)
                })
            },

            getMainLang() {
                axios.get('/dashboard/settings/getByKey/lang')
                    .then(res => {
                        this.mainLang = res.data.value;
                        this.obj.lang = this.getItem(this.langs, 'key', this.mainLang);
                        this.getTypes();
                    }).catch(err => {
                        console.log(err);
                        this.getTypes();
                });
            },

            getLangs() {
                axios.get('/dashboard/languages/get')
                    .then(res => {
                        this.langs = res.data;
                        this.getMainLang();
                    }).catch(err => {
                    console.log(err)
                })
            },

            getItem(arr, key, val) {

                let item = val;

                arr.forEach(it => {
                    if (it[key] === val) {
                        item = it;
                    }
                });

                return item;
            },

            getMenu() {
                let self = this;
                if (self._id) {
                    axios.get('/dashboard/menu/get?id=' + self._id)
                        .then(res => {
                            self.obj = res.data;

                            self.obj.type = self.getItem(self.types, 'id', self.obj.type);

                            if (self.lang !== self.obj.lang) {
                                self.obj.lang   = self.lang;
                                self.obj.id     = 0;
                            }
                            self.obj.lang = self.getItem(self.langs, 'key', self.obj.lang);

                            if (typeof res.data.structure === 'undefined') {
                                self.obj.structure = [];
                            }
                        }).catch(err => {
                        console.log(err)
                    })
                }
            },

            validate(e) {
                e.preventDefault();
                if (this.obj.name) {
                    let formData = new FormData();
                    formData.append('_method', this.obj.id ? 'PUT' : 'POST');
                    formData.append('obj', JSON.stringify(this.obj));
                    formData.append('translation', this._id);

                    axios.post(this.url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(res => {
                            window.location = res.data.redirect;
                        }).catch(err => {
                        console.log(err);
                    });
                } else {
                    this.errors.name = ['To pole jest wymagane'];
                    return false;
                }
            }
        },

        watch: {
            'obj.name'() {
                if (!this.obj.name) {
                    this.errors.name = ['To pole jest wymagane'];
                }
            }
        }
    }
</script>
