<template>
    <div :id="id" uk-modal ref="addMemberDialog">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-child-width-1-1" uk-grid>
                <div>
                    <h2 class="uk-modal-title">{{ $t('add_members') }}</h2>
                    <p class="uk-text-meta" v-if="team.team_type">
                        {{ $t('add_members_info') }}
                    </p>
                </div>
                <div>
                    <form class="uk-form uk-child-width-1-4 uk-flex-middle" v-if="! team.team_type" uk-grid>
                        <div>
                            <uikit-input-text v-model="start_age" id="start_age">
                                {{ $t('min_age') }}:
                            </uikit-input-text>
                        </div>
                        <div>
                            <uikit-input-text v-model="end_age" id="end_age">
                                {{ $t('max_age') }}:
                            </uikit-input-text>
                        </div>
                        <div>
                            <uikit-select v-model="gender" :items="genders">
                                {{ $t('gender') }}:
                            </uikit-select>
                        </div>
                        <div>
                            <label class="uk-form-label">&nbsp;</label>
                            <button class="uk-button uk-button-primary" @click="filterAvailableMembers">
                                {{ $t('filter') }}
                            </button>
                        </div>
                    </form>
                    <p v-if="! team.team_type" class="uk-text-meta">
                        {{ $t('use_filter') }}
                    </p>
                    <p class="uk-text-meta" v-if="team.season && availableMembers.length > 0" v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"></p>
                    <hr />
                </div>
                <div v-if="$wait.is('teams.availableMembers')" class="uk-flex-center" uk-grid>
                    <div class="uk-text-center">
                        <i class="fas fa-spinner fa-2x fa-spin"></i>
                    </div>
                </div>
                <div class="uk-overflow-auto uk-height-medium">
                    <table v-if="availableMembers.length > 0" class="uk-table uk-table-small uk-table-middle uk-table-divider">
                        <tr v-for="member in availableMembers" :key="member.id">
                            <td>
                                <input class="uk-checkbox" type="checkbox" v-model="selectedAvailableMembers" :value="member.id">
                            </td>
                            <td>
                                <strong>{{ member.person.name }}</strong><br />
                                {{ member.person.formatted_birthdate }} ({{ memberAge(member) }})
                            </td>
                            <td>
                                {{ member.license }}<br />
                                <i class="fas fa-male" v-if="member.person.gender == 1"></i>
                                <i class="fas fa-female" v-if="member.person.gender == 2"></i>
                                <i class="fas fa-question" v-if="member.person.gender == 0"></i>
                            </td>
                        </tr>
                    </table>
                    <p v-else-if="team.team_type">
                        {{ $t('no_available_members') }}
                    </p>
                </div>
                <div>
                    <hr />
                    <button class="uk-button uk-button-default" @click="hideAddMemberDialog">
                        <i class="fas fa-ban"></i>&nbsp; {{ $t('cancel') }}
                    </button>
                    <button class="uk-button uk-button-primary" :disabled="selectedAvailableMembers.length == 0" @click="addMembers">
                        <i class="fas fa-plus"></i>&nbsp; {{ $t('add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitSelect from '@/components/uikit/Select.vue';

    import UIkit from 'uikit';

    import messages from './lang';

    import Member from '@/models/Member';

    export default {
        components : {
            UikitInputText,
            UikitSelect
        },
        i18n : messages,
        props : [ 'id', 'team' ],
        data() {
            return {
                selectedAvailableMembers : [],
                start_age : 0,
                end_age : 0,
                gender : 0,
                genders : [
                    { text : 'None', value : 0 },
                    { text : 'Male', value : 1 },
                    { text : 'Female', value : 2 }
                ]
            }
        },
        computed : {
            availableMembers() {
                return this.$store.getters['teamModule/availableMembers'];
            }
        },
        mounted() {
            UIkit.util.on('#' + this.id, 'beforeshow', () => {
                this.selectedAvailableMembers = [];
                if (this.team.team_type) {
                    this.$store.dispatch('teamModule/availableMembers', { id : this.team.id })
                        .catch((err) => {
                            console.log(err);
                        });
                }
            });
        },
        methods : {
            filterAvailableMembers() {
                this.$store.dispatch('teamModule/availableMembers', {
                    id : this.$route.params.id,
                    filter : {
                        start_age : this.start_age,
                        end_age : this.end_age,
                        gender : this.gender
                    }
                });
            },
            hideAddMemberDialog() {
                var modal = UIkit.modal(this.$refs.addMemberDialog);
                modal.hide();
            },
            addMembers() {
                var members = [];
                this.selectedAvailableMembers.forEach((id) => {
                    var member = new Member();
                    member.id = id;
                    members.push(member);
                });
                this.$store.dispatch('teamModule/addMembers', {
                    id : this.$route.params.id,
                    members : members
                });
                var modal = UIkit.modal(this.$refs.addMemberDialog);
                modal.hide();
            },
            memberAge(member) {
                if (this.team.season) {
                    return this.team.season.end_date.diff(member.person.birthdate, 'years');
                }
                return member.person.age;
            }
        }
    };
</script>
