
document.addEventListener('alpine:init', () => {
    console.log('AlpineJS initialized');
    Alpine.data('progressBar', () => ({
        progressBarWidth: 1,
        currentStatus: '',
        init() {
            console.log('upload completed')

            Echo.private('import-status.1')
                .listen('ImportCompleted', (e) => {
                    this.currentStatus = e.status;
                    this.updateProgressBar();
                });
        },


    }));
});