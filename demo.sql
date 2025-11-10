
SET NOCOUNT ON
GO

set nocount    on
set dateformat mdy

USE master

declare @dttm varchar(55)
select  @dttm=convert(varchar,getdate(),113)
raiserror('Beginning InstPubs.SQL at %s ....',1,1,@dttm) with nowait

GO

if exists (select * from sysdatabases where name='pubs')
begin
  raiserror('Dropping existing pubs database ....',0,1)
  DROP database pubs
end
GO

CHECKPOINT
go

raiserror('Creating pubs database....',0,1)
go
/*
   Use default size with autogrow
*/

CREATE DATABASE pubs
GO

CHECKPOINT

GO

USE pubs

GO

if db_name() <> 'pubs'
   raiserror('Error in InstPubs.SQL, ''USE pubs'' failed!  Killing the SPID now.'
            ,22,127) with log

GO

if CAST(SERVERPROPERTY('ProductMajorVersion') AS INT)<12
BEGIN
  exec sp_dboption 'pubs','trunc. log on chkpt.','true'
  exec sp_dboption 'pubs','select into/bulkcopy','true'
END
ELSE ALTER DATABASE [pubs] SET RECOVERY SIMPLE WITH NO_WAIT
GO

execute sp_addtype id      ,'varchar(11)' ,'NOT NULL'
execute sp_addtype tid     ,'varchar(6)'  ,'NOT NULL'
execute sp_addtype empid   ,'char(9)'     ,'NOT NULL'

raiserror('Now at the create table section ....',0,1)

GO

CREATE TABLE authors
(
   au_id          id

         CHECK (au_id like '[0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9][0-9][0-9]')

         CONSTRAINT UPKCL_auidind PRIMARY KEY CLUSTERED,

   au_lname       varchar(40)       NOT NULL,
   au_fname       varchar(20)       NOT NULL,

   phone          char(12)          NOT NULL

         DEFAULT ('UNKNOWN'),

   address        varchar(40)           NULL,
   city           varchar(20)           NULL,
   state          char(2)               NULL,

   zip            char(5)               NULL

         CHECK (zip like '[0-9][0-9][0-9][0-9][0-9]'),

   contract       bit               NOT NULL
)

GO

CREATE TABLE publishers
(
   pub_id         char(4)           NOT NULL

         CONSTRAINT UPKCL_pubind PRIMARY KEY CLUSTERED

         CHECK (pub_id in ('1389', '0736', '0877', '1622', '1756')
            OR pub_id like '99[0-9][0-9]'),

   pub_name       varchar(40)           NULL,
   city           varchar(20)           NULL,
   state          char(2)               NULL,

   country        varchar(30)           NULL

         DEFAULT('USA')
)

GO

CREATE TABLE titles
(
   title_id       tid

         CONSTRAINT UPKCL_titleidind PRIMARY KEY CLUSTERED,

   title          varchar(80)       NOT NULL,

   type           char(12)          NOT NULL

         DEFAULT ('UNDECIDED'),

   pub_id         char(4)               NULL

         REFERENCES publishers(pub_id),

   price          money                 NULL,
   advance        money                 NULL,
   royalty        int                   NULL,
   ytd_sales      int                   NULL,
   notes          varchar(200)          NULL,

   pubdate        datetime          NOT NULL

         DEFAULT (getdate())
)

GO

CREATE TABLE titleauthor
(
   au_id          id

         REFERENCES authors(au_id),

   title_id       tid

         REFERENCES titles(title_id),

   au_ord         tinyint               NULL,
   royaltyper     int                   NULL,


   CONSTRAINT UPKCL_taind PRIMARY KEY CLUSTERED(au_id, title_id)
)

GO

CREATE TABLE stores
(
   stor_id        char(4)           NOT NULL

         CONSTRAINT UPK_storeid PRIMARY KEY CLUSTERED,

   stor_name      varchar(40)           NULL,
   stor_address   varchar(40)           NULL,
   city           varchar(20)           NULL,
   state          char(2)               NULL,
   zip            char(5)               NULL
)

GO

CREATE TABLE sales
(
   stor_id        char(4)           NOT NULL

         REFERENCES stores(stor_id),

   ord_num        varchar(20)       NOT NULL,
   ord_date       datetime          NOT NULL,
   qty            smallint          NOT NULL,
   payterms       varchar(12)       NOT NULL,

   title_id       tid

         REFERENCES titles(title_id),


   CONSTRAINT UPKCL_sales PRIMARY KEY CLUSTERED (stor_id, ord_num, title_id)
)

GO

CREATE TABLE roysched
(
   title_id       tid

         REFERENCES titles(title_id),

   lorange        int                   NULL,
   hirange        int                   NULL,
   royalty        int                   NULL
)

GO

CREATE TABLE discounts
(
   discounttype   varchar(40)       NOT NULL,

   stor_id        char(4) NULL

         REFERENCES stores(stor_id),

   lowqty         smallint              NULL,
   highqty        smallint              NULL,
   discount       dec(4,2)          NOT NULL
)

GO

CREATE TABLE jobs
(
   job_id         smallint          IDENTITY(1,1)

         PRIMARY KEY CLUSTERED,

   job_desc       varchar(50)       NOT NULL

         DEFAULT 'New Position - title not formalized yet',

   min_lvl        tinyint           NOT NULL

         CHECK (min_lvl >= 10),

   max_lvl        tinyint           NOT NULL

         CHECK (max_lvl <= 250)
)

GO

CREATE TABLE pub_info
(
   pub_id         char(4)           NOT NULL

         REFERENCES publishers(pub_id)

         CONSTRAINT UPKCL_pubinfo PRIMARY KEY CLUSTERED,

   logo           image                 NULL,
   pr_info        text                  NULL
)

GO

CREATE TABLE employee
(
   emp_id         empid

         CONSTRAINT PK_emp_id PRIMARY KEY NONCLUSTERED

         CONSTRAINT CK_emp_id CHECK (emp_id LIKE
            '[A-Z][A-Z][A-Z][1-9][0-9][0-9][0-9][0-9][FM]' or
            emp_id LIKE '[A-Z]-[A-Z][1-9][0-9][0-9][0-9][0-9][FM]'),

   fname          varchar(20)       NOT NULL,
   minit          char(1)               NULL,
   lname          varchar(30)       NOT NULL,

   job_id         smallint          NOT NULL

         DEFAULT 1

         REFERENCES jobs(job_id),

   job_lvl        tinyint

         DEFAULT 10,

   pub_id         char(4)           NOT NULL

         DEFAULT ('9952')

         REFERENCES publishers(pub_id),

   hire_date      datetime          NOT NULL

         DEFAULT (getdate())
)

GO

raiserror('Now at the create trigger section ...',0,1)

GO

CREATE TRIGGER employee_insupd
ON employee
FOR insert, UPDATE
AS
--Get the range of level for this job type from the jobs table.
declare @min_lvl tinyint,
   @max_lvl tinyint,
   @emp_lvl tinyint,
   @job_id smallint
select @min_lvl = min_lvl,
   @max_lvl = max_lvl,
   @emp_lvl = i.job_lvl,
   @job_id = i.job_id
from employee e, jobs j, inserted i
where e.emp_id = i.emp_id AND i.job_id = j.job_id
IF (@job_id = 1) and (@emp_lvl <> 10)
begin
   raiserror ('Job id 1 expects the default level of 10.',16,1)
   ROLLBACK TRANSACTION
end
ELSE
IF NOT (@emp_lvl BETWEEN @min_lvl AND @max_lvl)
begin
   raiserror ('The level for job_id:%d should be between %d and %d.',
      16, 1, @job_id, @min_lvl, @max_lvl)
   ROLLBACK TRANSACTION
end

GO

raiserror('Now at the inserts to authors ....',0,1)

GO