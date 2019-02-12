/* eslint-disable */
const createError = require('http-errors');
const express = require('express');
const expressLayouts = require('express-ejs-layouts');
const cookieParser = require('cookie-parser');
const logger = require('morgan');
const opn = require('opn');
const path = require('path');

const app = express();

process.argv.forEach((value) => {
    if (value === '-mocha' || value === '-m') {

        const port = 9988;

        app.use(express.static(`${__dirname}/report`));
        app.get('/', (req, res) => {
            res.sendFile(path.join(`${__dirname}/report/mochawesome.html`))
        });
        app.listen(port, () => console.log(`Test Report shows on port ${port}`));

    } else if (value === '-express' || value === '-e') {

        // Routers
        const indexRouter = require('./report/routes/index');
        const usersRouter = require('./report/routes/users');

        const port = 1488;

        // view engine setup
        app.set('views', path.join(__dirname, '/report/views'));
        app.set('view engine', 'ejs');
        app.set('layout extractScripts', true);
        app.set('layout extractStyles', true);

        app.use(logger('dev'));
        app.use(express.json());
        app.use(express.urlencoded({ extended: false }));
        app.use(cookieParser());
        app.use(express.static(path.join(__dirname, '/report/public')));
        app.listen(port, () => console.log(`Express server shows on port ${port}`));
        // setTimeout(() => { opn(`http://localhost:${port}`); }, 3000);

        app.use(expressLayouts);
        app.use('/', indexRouter);
        app.use('/integration', indexRouter);
        app.use('/users', usersRouter);

        // error handler
        app.use((err, req, res, next) => {
            // set locals, only providing error in development
            res.locals.message = err.message;
            res.locals.error = req.app.get('env') === 'development' ? err : {};

            // render the error page
            res.status(err.status || 500);
            res.render('error');
        });

    }
});

module.exports = app;