<template>
  <div>
      <data-driven-data-table
          schema="BookSchema"
          :search="search"
      />
  </div>
</template>

<script>
import DataDrivenDataTable from "../common/DataDrivenDataTable";

export default {
  name: "Dashboard",
  components: {DataDrivenDataTable},

  data() {
    return {
      search: ""
    };
  }
}
</script>

<style scoped>

</style>