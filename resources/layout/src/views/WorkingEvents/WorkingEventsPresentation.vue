<template>
  <div class="v-workingEventsPresentation">
    <vue-horizontal-timeline :items="items" />
    <debugger :keepAlive="true" :components="$children"></debugger>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { Timeline, TimelineTitle, TimelineItem } from 'vue-cute-timeline'
import 'vue-cute-timeline/dist/index.css'
import { VueHorizontalTimeline } from 'vue-horizontal-timeline'

@Component({
  components: {
    Timeline,
    TimelineTitle,
    TimelineItem,
    VueHorizontalTimeline
  }
})
export default class WorkingEventsPresentation extends Vue {
  public get items (): any[] {
    return [
      {
        title: 'Logowanie',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 08:00:00' +
          ''
      },
      {
        title: 'Wejście na zamówienie',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 08:05:00 ' +
          'Zamówienie id: 1232 '
      },
      {
        title: 'Bezczynność',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 08:10:00 ' +
          'Czas trwania: 15 minut ' +
          'powód: Rozmowa z klientem '
      },
      {
        title: 'Chat z klientem',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 09:00:00'
      },
      {
        title: 'Wejście na zamówienie',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 09:05:00 ' +
          'Zamówienie id: 12323 '
      },
      {
        title: 'Bezczynność',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 12:30:00 ' +
          'Czas trwania: 15 minut ' +
          'powód: Byłem w sklepie'
      },
      {
        title: 'Bezczynność',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 13:10:00 ' +
          'Czas trwania: 30 minut ' +
          'powód: jadłem obiad'
      },
      {
        title: 'Zmiana daty zamówienia',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 14:00:00' +
          ''
      },
      {
        title: 'Wejście na zamówienie',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 14:30:00 ' +
          'Zamówienie id: 1234322 '
      },
      {
        title: 'Bezczynność',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 14:50:00 ' +
          'Czas trwania: 15 minut ' +
          'powód: Rozmowa z klientem '
      },

      {
        title: 'Wejście na zamówienie',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 15:05:00 ' +
          'Zamówienie id: 1232 '
      },
      {
        title: 'Bezczynność',
        content:
          'Użytkownik: Adam nowak ' +
          'Godzina: 15:10:00 ' +
          'Czas trwania: 15 minut ' +
          'powód: Rozmowa z klientem '
      }
    ]
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .voyager-pen,
  .voyager-trash {
    @media (min-width: 992px) {
      margin-right: 7px;
    }
  }

  .voyager-double-down,
  .voyager-double-up {
    @media (min-width: 992px) {
      margin-right: 4px;
    }
  }
</style>
