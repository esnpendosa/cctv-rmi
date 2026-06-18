import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.rozitech.cctvmanager',
  appName: 'CCTV Manager',
  webDir: 'public_mobile',
  server: {
    url: 'http://10.0.2.2:8000', // Points to local development host from Android emulator
    cleartext: true
  },
  android: {
    allowMixedContent: true
  }
};

export default config;
