FROM centos:7
RUN yum update -y && \
    curl -o /etc/yum.repos.d/jp3cki.repo https://rpm.fetus.jp/jp3cki.repo && \
    yum-config-manager --enable jp3cki-h2o-mainline && \
    yum install -y h2o && \
    rm /etc/yum.repos.d/jp3cki.repo && \
    yum clean all
ADD h2o.conf /etc/h2o/h2o.conf
ADD web/ /var/www/site/web/
CMD ["/usr/bin/h2o", "-m", "master", "-c", "/etc/h2o/h2o.conf"]
EXPOSE 80
