/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import { startStimulusApp } from '@symfony/stimulus-bridge';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// Register Chart.js elements
import { Chart } from 'chart.js';
//import { registerChart } from '@symfony/ux-chartjs';
registerChart(Chart);

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
