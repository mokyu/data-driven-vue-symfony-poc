<template>
  <div>
    <span v-if="type === 'Text'">
      {{ value || '-' }}
    </span>
    <span
        v-else-if="type === 'RichText'"
        style="white-space: pre-wrap;"
    >{{ value || '-' }}</span>
    <span v-else-if="type === 'List'">
      {{ listValue }}
    </span>
    <span v-else-if="type === 'DemolitionDateHeader'">
      {{ dateText || '-' }}
    </span>
    <span v-else-if="type === 'CropExpirationHeader'">
      {{ dateText || '-' }}
    </span>
    <span v-else-if="type === 'FutureDateCountDown'">
      {{ dateText || '-' }}
    </span>
    <span v-else>
      {{ value }}
    </span>
  </div>
</template>

<script>
const moment = require('moment');

export default {
  name: "DataDrivenDataTableCell",
  props: {
    type: {
      required: true
    },
    header: {
      required: true
    },
    value: {
      required: true
    },
    listItems: {
      required: false,
      default: []
    }
  },

  data() {
    return {
      dateText: null
    };
  },

  computed: {
    listValue() {
      const result = this.listItems.find(item => item[this.header.listKey || 'key'] === this.value);
      return result[this.header.listValue || 'value'] || '-';
    }
  },

  watch: {
    value() {
      this.refreshDates();
    }
  },

  mounted() {
    this.refreshDates();
  },

  methods: {
    refreshDates() {
      if (this.type === 'DemolitionDateHeader' && this.value !== null) {
        this.dateText = moment.duration(moment(this.value).add(45, 'days').diff(moment())).humanize(true, {d: 99});
      } else if (this.type === 'CropExpirationHeader' && this.value !== null) {
        this.dateText = moment.duration(moment(this.value).add(24, 'hours').diff(moment())).humanize(true, {h: 999});
      } else if (this.type === 'FutureDateCountDown' && this.value !== null) {
        this.dateText = moment.duration(moment(this.value).diff(moment())).humanize(true, {h: 999});
      } else {
        this.dateText = null;
      }
    }
  }
}
</script>

<style scoped>

</style>