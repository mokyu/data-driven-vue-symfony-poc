<template>
  <div>
    <template v-if="formInputData.fieldType === 'Text'">
      <v-text-field
        :value="value"
        @input="data => $emit('input', data)"
        :rules="getRules(formInputData)"
        :placeholder="formInputData.placeholder"
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'Number'">
      <v-text-field
          :value="value"
          @input="data => $emit('input', Math.round(Number(data)))"
          :rules="getRules(formInputData)"
          type="number"
          clearable
          :placeholder="formInputData.placeholder"
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'Float'">
      <v-text-field
          :value="value"
          @input="data => $emit('input', Number(data))"
          :rules="getRules(formInputData)"
          type="number"
          clearable
          :placeholder="formInputData.placeholder"
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'List'">
      <v-autocomplete
          :value="value"
          :loading="isLoadingItems"
          @input="data => $emit('input', data)"
          :rules="getRules(formInputData)"
          :items="items"
          :item-text="formInputData.listValue || 'value'"
          :item-value="formInputData.listKey || 'key'"
          :placeholder="formInputData.placeholder"
          clearable
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'RichText'">
      <v-textarea
          :value="value"
          @input="data => $emit('input', data)"
          :rules="getRules(formInputData)"
          :placeholder="formInputData.placeholder"
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'Checkbox'">
      <v-checkbox
          :value="value"
          @input="data => $emit('input', data)"
          :rules="getRules(formInputData)"
          :label="formInputData.placeholder"
      />
    </template>
    <template v-else-if="formInputData.fieldType === 'DemolitionDateForm'">
      <v-text-field
          :value="getCountDownDate()"
          placeholder="No date set, press the icon on the right to update this field."
          readonly
          append-outer-icon="mdi-update"
          @click:append-outer="$emit('input', new Date().toISOString())"
          @click:clear="$emit('input', null)"
          clearable
      />
    </template>

    <template v-else-if="formInputData.fieldType === 'FutureDateSelector'">
      <template v-if="value === null">
        <v-text-field
            label="days"
            type="number"
            v-model.number="futureDateFields.days"
            :rules="[
              v =>  (v >= 0 && Number.isInteger(v)) || 'Invalid'
          ]"
            style="max-width: 80px; float: left;"
        />
        <v-text-field
            label="hours"
            type="number"
            v-model.number="futureDateFields.hours"
            :rules="[
              v =>  (v >= 0 && v < 24 && Number.isInteger(v)) || 'Invalid'
          ]"
            style="max-width: 80px; float: left;"
            class="ml-4"
        />
        <v-text-field
            label="minutes"
            type="number"
            v-model.number="futureDateFields.minutes"
            :rules="[
              v =>  (v >= 0 && v < 60 && Number.isInteger(v)) || 'Invalid'
          ]"
            style="max-width: 80px; float: left;"
            class="mx-4"
        />
        <v-btn class="mt-3" @click="setCountDownDate">
          Set
        </v-btn>
      </template>
      <template v-else>
        <v-text-field
          :value="getCountDownDate()"
          style="max-width: 250px; float: left;"
          readonly
        />
        <v-btn class="mt-3 ml-4" @click="$emit('input', null)">
          Clear
        </v-btn>
      </template>
    </template>

    <template v-else>
      Unknown fieldType: {{formInputData.fieldType}}
    </template>
  </div>
</template>

<script>
import axios from "axios";
const moment = require('moment');

export default {
name: "DataDrivenInput",
  props: {
    value: {
      required: true
    },
    formInputData: {
      required: true
    }
  },

  data() {
    return {
      items: [],
      isLoadingItems: false,
      futureDateFields: {
        days: 0,
        hours: 0,
        minutes: 0
      },
    };
  },

  methods: {
    getRules() {
      let rules = [];
      if (this.formInputData.maxLength !== -1) {
        rules.push(v => (v || '').length <= this.formInputData.maxLength || `The maximum length of ${this.formInputData.maxLength} characters has been exceeded`);
      }
      if (this.formInputData.required) {
        rules.push(v => (!!v || v === '0' || v === 0) || `This is a required field`);
      }
      return rules;
    },

    getCountDownDate() {
      if (this.value) {
        return moment.duration(moment(this.value).diff(moment())).humanize(true);
      }
      return null;
    },

    setCountDownDate() {
      this.$emit(
          'input',
          moment()
              .add(this.futureDateFields.days, 'days')
              .add(this.futureDateFields.hours, 'hours')
              .add(this.futureDateFields.minutes, 'minutes')
              .toISOString()
      )
    }
  },

  async mounted() {
    this.isLoadingItems = true;
    if (this.formInputData.listFromSchema) {
      this.items = (await axios.get(`/api/data/${this.formInputData.listFromSchema || this.formInputData.listFromEnum}/list`)).data;
    } else if (this.formInputData.listFromEnum) {
      this.items = (await axios.get(`/api/enum/${this.formInputData.listFromEnum}/list`)).data;
    }
    this.isLoadingItems = false;
  }
}
</script>

<style scoped>

</style>