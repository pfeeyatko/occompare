<template>
    <div class="container py-3">
        <div class="row">
            <div class="col-12 text-center">
                <div class="form">
                    <div class="form-group row">
                        <div class="col-md-5">
                            <label>Occupation 1</label>
                            <select-occupation v-model="occupation_1"></select-occupation>
                        </div>
                        <div class="col-md-5">
                            <label>Occupation 2</label>
                            <select-occupation v-model="occupation_2"></select-occupation>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-danger btn-block mt-4" @click.prevent="compare" :disabled="!occupation_1 || !occupation_2 || loading">
                                <template v-if="loading">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </template>
                                <template v-else>
                                    Compare
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <template v-if="match && !loading">
                <div class="col-12">
                    <h3>Match percentage:</h3>
                    <div class="progress">
                        <div class="progress-bar" :class="percentageClass()" :style="{width: match+'%'}" :aria-valuenow="match" aria-valuemin="0" aria-valuemax="100">{{ match }}%</div>
                    </div>
                </div>
            </template>
            <template v-else-if="!match && !loading && error_msg">
                <div class="col-12 text-center">
                    <div class="alert alert-danger" role="alert">
                        {{ error_msg }}
                    </div>
                </div>
            </template>
            <template v-else-if="!match && !loading">
                <div class="col-12 text-center">
                    Please select two Occupations from above and click Compare
                </div>
            </template>
            <template v-else-if="loading">
                <div class="col-12 text-center">
                    Please wait...
                </div>
            </template>
        </div>

        <!---->
        <!---->
        <!--  Use this space to visualise and present the result/breakdown or whatever you see fit  -->
        <template v-if="skills_intersect && !loading">
            <div class="skill-intersect">
                <h3>Skills which intersect <small>(ordered by importance):</small></h3>
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item" v-for="(skill, index) in skills_intersect" :key="index">{{ index + 1 }}. {{ skill }}</li>
                </ol>
            </div>
        </template>
        <!---->
        <!---->

    </div>
</template>

<script>
    import SelectOccupation from '../components/form-controls/SelectOccupation';
    export default {
        name: 'home-page',
        components: {
            SelectOccupation
        },
        data() {
            return {
                loading: false,
                occupation_1: null,
                occupation_2: null,
                match: null,
                skills_intersect: null,
                error_msg: null,
            }
        },
        created() {
            this.MATCH = {
                GOOD: 80,
                OK: 50,
            };
        },
        methods: {
            compare() {
                this.loading = true;
                this.axios.post('/api/compare', {
                    occupation_1: this.occupation_1,
                    occupation_2: this.occupation_2
                }).then((response) => {
                    this.loading = false;
                    this.match = response.data.match;
                    this.skills_intersect = response.data.skills_intersect;
                }).catch((error) => {
                    if (error.response) {
                        this.error_msg = error.response.data.message;
                    }
                    this.match = null;
                    this.loading = false;
                    this.skills_intersect = null;
                });
            },
            percentageClass() {
                if (this.match >= this.MATCH.GOOD) {
                    return 'bg-success'
                } else if (this.match >= this.MATCH.OK) {
                    return 'bg-info';
                }
                return 'bg-warning';
            }
        }
    }
</script>

<style lang="scss" scoped>
    .form-group {
        label {
            font-size: 0.8rem;
            text-align: left;
            display: block;
            margin-bottom: 0.2rem
        }
    }

    .progress {
        height: 30px;

        .progress-bar {
            font-size: 1rem;
        }
    }

    .skill-intersect {
        margin-top: 25px;
    }
</style>