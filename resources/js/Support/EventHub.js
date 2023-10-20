import mitt from 'mitt';

const m = mitt();

m.on('*', (...args) => {
    console.info(`%c${args[0]}%c emit by [%cmitt%c]`, 'color: #881391; font-weight: semibold', 'color: #999; font-weight: normal', 'color: #3ba776', 'color: #999; font-weight: normal');
});

export default m;
