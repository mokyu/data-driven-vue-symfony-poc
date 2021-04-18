<template>
  <v-app>
    <v-card
        color="grey lighten-4"
        flat
        height="100%"
        tile
    >
      <v-toolbar dense>
        <v-toolbar-title>De Boekenclub</v-toolbar-title>
        <v-spacer/>
        <v-btn icon>
          <v-icon @click="$router.push('/')">mdi-bookshelf</v-icon>
        </v-btn>

        <v-btn icon>
          <v-icon @click="$router.push('/members')">mdi-account-multiple</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col>
              <router-view/>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-app>
</template>

<script>
export default {
  name: "App"
}
</script>

<style scoped>

</style>